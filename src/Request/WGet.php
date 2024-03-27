<?php
/**
 * @author Avid
 * 
 * WGet API Request Method
 */
namespace Bottel\Request;
use Bottel\Utils\DelayedResponse;
use Bottel\Types\RawRequest;

class WGet extends RequestDriver {
    /**
     * WGet request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return DelayedResponse|false
     */
    public function request(string $method, array $datas = []): DelayedResponse|bool {
        $this->last_request = new RawRequest($method, $datas);

        $tghost = config("TG_HOST", 'api.telegram.org');
        $url = config("TG_SCHEME", 'http') . "://$tghost:" . config("TG_PORT", 80) . "/bot{$this->token}/$method?" . http_build_query($datas);
        $res = file_get_contents($url);
        if(!$res){
            return false;
        }
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
     * WGet read file
     * 
     * @method file
     * @param string $path
     * @return string|false
     */
    public function file(string $path): string|false {
        $tghost = config("TG_HOST", 'api.telegram.org');
        if(config("TG_METHOD") == 'GET'){
            $url = config("TG_SCHEME", 'http') . "://$tghost:" . config("TG_PORT", 80) . "/file/bot{$this->token}/$path";
        }else{
            $url = config("TG_SCHEME", 'https') . "://$tghost:" . config("TG_PORT", 443) . "/file/bot{$this->token}/$path";
        }
        $res = file_get_contents($url);
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
     * WGet download file
     * 
     * @method file
     * @param string $path
     * @param string $into
     * @return bool
     */
    public function download(string $path, string $into): bool {
        $tghost = config("TG_HOST", 'api.telegram.org');
        if(config("TG_METHOD") == 'GET'){
            $url = config("TG_SCHEME", 'http') . "://$tghost:" . config("TG_PORT", 80) . "/file/bot{$this->token}/$path";
        }else{
            $url = config("TG_SCHEME", 'https') . "://$tghost:" . config("TG_PORT", 443) . "/file/bot{$this->token}/$path";
        }
        $read = fopen($url, 'rb');
        if(!$read){
            return false;
        }
        $write = fopen($into, 'wb');
        if(!$write){
            return false;
        }
        $packet = fread($read, 1024);
        if($packet == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            $this->last_error_code = 404;
            $this->last_error_message = "Not Found";
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
        fwrite($write, $packet);
        stream_copy_to_stream($read, $write);
        fclose($read);
        fclose($write);
        return true;
    }
}
?>