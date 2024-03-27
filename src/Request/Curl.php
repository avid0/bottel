<?php
/**
 * @author Avid
 * 
 * CURL API Request Method
 */
namespace Bottel\Request;
use Bottel\Utils\DelayedResponse;
use Bottel\Types\RawRequest;

class Curl extends RequestDriver {
    /**
     * Proxy address
     * @var string $proxy_address
     */
    public $proxy_address;

    /**
     * Proxy auth
     * @var $proxy_auth = PROXY_HTTP (default)
     */
    public $proxy_auth = 0;

    /**
     * Proxy type
     * @var int $proxy_type
     */
    public $proxy_type;

    /**
     * CURL handle proxy init
     * 
     * @method initProxy
     * @param \CurlHandle $ch
     * @return void
     */
    private function initProxy(\CurlHandle $ch): void {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy_address);
        if($this->proxy_auth)
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_auth);
        if(is_int($this->proxy_type))
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxy_type);
    }

    /**
     * CURL request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return DelayedResponse|false
     */
    public function request(string $method, array $datas = []): DelayedResponse|false {
        $this->last_request = new RawRequest($method, $datas);

        $tghost = config("TG_HOST", 'api.telegram.org');
        if(config("TG_METHOD") == 'GET'){
            $url = config("TG_SCHEME", 'http') . "://$tghost:" . config("TG_PORT", 80) . "/bot{$this->token}/$method?" . http_build_query($datas);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }else{
            $url = config("TG_SCHEME", 'https') . "://$tghost:" . config("TG_PORT", 443) . "/bot{$this->token}/$method";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        if($this->proxy_address){
            $this->initProxy($ch);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        if(!$res){
            return false;
        }
        curl_close($ch);
        $res = json_decode($res, true);
        if(!isset($res['ok']) || !$res['ok']){
            $this->last_error_code = $res['error_code'] ?? 100;
            $this->last_error_message = $res['description'] ?? 'Unknown error';
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
        return new DelayedResponse($res['result']);
    }

    /**
     * CURL read file
     * 
     * @method file
     * @param string $path
     * @return string|false
     */
    public function file(string $path): string|false {
        $tghost = config("TG_HOST", 'api.telegram.org');
        if(config("TG_METHOD") == 'GET'){
            $url = config("TG_SCHEME", 'http') . "://$tghost:" . config("TG_PORT", 80) . "/file/bot{$this->token}/$path";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }else{
            $url = config("TG_SCHEME", 'https') . "://$tghost:" . config("TG_PORT", 443) . "/file/bot{$this->token}/$path";
            $ch = curl_init($url);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        if($this->proxy_address)
            $this->initProxy($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        if(!$res){
            return false;
        }
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            $this->last_error_code = 404;
            $this->last_error_message = "Not Found";
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
        return $res;
    }

    /**
     * CURL download file
     * 
     * @method file
     * @param string $path
     * @param string $into
     * @return bool
     */
    public function download(string $path, string $into): bool {
        $write = fopen($into, 'wb');
        if(!$write){
            return false;
        }
        $tghost = config("TG_HOST", 'api.telegram.org');
        if(config("TG_METHOD") == 'GET'){
            $url = config("TG_SCHEME", 'http') . "://$tghost:" . config("TG_PORT", 80) . "/file/bot{$this->token}/$path";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }else{
            $url = config("TG_SCHEME", 'https') . "://$tghost:" . config("TG_PORT", 443) . "/file/bot{$this->token}/$path";
            $ch = curl_init($url);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        if($this->proxy_address)
            $this->initProxy($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $write);
        $res = curl_exec($ch);
        if(!$res){
            $this->last_error_code = 404;
            $this->last_error_message = "Not Found";
            return false;
        }
        fclose($write);
        $this->last_error_code = 200;
        $this->last_error_message = '';
        return $res;
    }
}

?>