<?php
/**
 * @author Avid
 * 
 * WGet API Request Method
 */
namespace Bottel\Request;
use Bottel\Utils\DelayedResponse;
use Bottel\Types\RawRequest;

class Socket extends RequestDriver {
    /**
     * Resource cache for multi-connection api request
     * @static
     * @var array $socks
     */
    public static $socks = array();

    /**
     * Send socket request to api.telegram.org without read result by multi-connection
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return DelayedResponse|false
     */
    public function request(string $method, array $datas = []): DelayedResponse|false {
        $this->last_request = new RawRequest($method, $datas);
        
        $path = "/bot{$this->token}/$method";
        $tgmethod = config("TG_METHOD");
        $tghost = config("TG_HOST", 'api.telegram.org');
        if($tgmethod == 'GET'){
            $protocol = config("TG_SCHEME", 'http') == 'http' ? 'tcp' : 'tls';
            $sock = fsockopen("$protocol://$tghost", config("TG_PORT", 80));
        }else{
            $protocol = config("TG_SCHEME", 'https') == 'http' ? 'tcp' : 'tls';
            $sock = fsockopen("$protocol://$tghost", config("TG_PORT", 443));
        }
        if(!$sock){
            return false;
        }
        if($tgmethod == 'GET'){
            $datas = http_build_query($datas);
            $packet = "GET $path?$datas HTTP/1.1\r\n";
            $packet.= "Host: $tghost\r\n";
            $packet.= "Content-Length: 0\r\n";
            $packet.= "\r\n";
        }else{
            $datas = json_encode($datas);
            $len = strlen($datas);
            $packet = "POST $path HTTP/1.1\r\n";
            $packet.= "Host: $tghost\r\n";
            $packet.= "Content-Type: application/json\r\n";
            $packet.= "Content-Length: $len\r\n";
            $packet.= "\r\n$datas";
        }
        $len = fwrite($sock, $packet);
        if($len == 0){
            fclose($sock);
            return false;
        }
        return new DelayedResponse($sock, $this);
    }

    /**
     * Socket read file
     * 
     * @method file
     * @param string $path
     * @return string|false
     */
    public function file(string $path): string|false {
        $path = "/file/bot{$this->token}/$path";
        $tgmethod = config("TG_METHOD");
        $tghost = config("TG_HOST", 'api.telegram.org');
        if($tgmethod == 'GET'){
            $protocol = config("TG_SCHEME", 'http') == 'http' ? 'tcp' : 'tls';
            $sock = fsockopen("$protocol://$tghost", config("TG_PORT", 80));
        }else{
            $protocol = config("TG_SCHEME", 'https') == 'http' ? 'tcp' : 'tls';
            $sock = fsockopen("$protocol://$tghost", config("TG_PORT", 443));
        }
        if(!$sock){
            return false;
        }
        if($tgmethod == 'GET'){
            $packet = "GET $path HTTP/1.1\r\n";
            $packet.= "Host: $tghost\r\n";
            $packet.= "Content-Length: 0\r\n";
            $packet.= "\r\n";
        }else{
            $packet = "POST $path HTTP/1.1\r\n";
            $packet.= "Host: $tghost\r\n";
            $packet.= "Content-Length: 0\r\n";
            $packet.= "\r\n";
        }
        $len = fwrite($sock, $packet);
        if($len == 0){
            fclose($sock);
            return false;
        }
        $res = stream_get_contents($sock);
        fclose($sock);
        $res = explode("\r\n\r\n", $res, 2);
        $res = $res[1];
        if(!$res){
            return false;
        }
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            $this->last_error_code = 404;
            $this->last_error_message = "Not Found";
            return false;
        }
        return $res;
    }
    
    /**
     * Socket download file into
     * 
     * @method download
     * @param string $path
     * @param string $into
     * @return bool
     */
    public function download(string $path, string $into): bool {
        $path = "/file/bot{$this->token}/$path";
        $write = fopen($into, 'wb');
        if(!$write){
            return false;
        }
        $tghost = config("TG_HOST", 'api.telegram.org');
        if(config("TG_METHOD") == 'GET'){
            $protocol = config("TG_SCHEME", 'http') == 'http' ? 'tcp' : 'tls';
            $sock = fsockopen("$protocol://$tghost", config("TG_PORT", 80));
        }else{
            $protocol = config("TG_SCHEME", 'https') == 'http' ? 'tcp' : 'tls';
            $sock = fsockopen("$protocol://$tghost", config("TG_PORT", 443));
        }
        if(!$sock){
            return false;
        }
        if(config("TG_METHOD") == 'GET'){
            $packet = "GET $path HTTP/1.1\r\n";
            $packet.= "Host: $tghost\r\n";
            $packet.= "Content-Length: 0\r\n";
            $packet.= "\r\n";
        }else{
            $packet = "POST $path HTTP/1.1\r\n";
            $packet.= "Host: $tghost\r\n";
            $packet.= "Content-Length: 0\r\n";
            $packet.= "\r\n";
        }
        $len = fwrite($sock, $packet);
        if($len == 0){
            fclose($sock);
            return false;
        }
        while($packet = fread($sock, 4096))
            if(strpos($packet, "\r\n\r\n") !== false)
                break;
        if(!$packet){
            fclose($sock);
            return false;
        }
        $packet = explode("\r\n\r\n", $packet, 2);
        $res = $packet[1] . fread($sock, 64);
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            fclose($sock);
            fclose($write);
            return false;
        }
        fwrite($write, $res);
        if($res == $packet[1]){
            fclose($sock);
            fclose($write);
            return true;
        }
        unset($packet);
        stream_copy_to_stream($sock, $write);
        fclose($sock);
        fclose($write);
        return true;
    }

    /**
     * Wait for responsing all connections of multi-connection api request
     * 
     * @method wait
     * @return void
     */
    public function wait(): void {
        array_map('fgetc', self::$socks);
    }

    /**
     * Close all connections of multi-connection api request
     * 
     * @method close
     * @return void
     */
    public function close(): void {
        array_map('fclose', self::$socks);
        $this->clear();
    }

    /**
     * Clear all connections
     * 
     * @method clear
     * @return void
     */
    public function clear(): void {
        $this->socks = array();
    }
}
?>