<?php
/**
 * @author Avid
 * 
 * Amp Http-client API Request Method
 */
namespace Bottel\Request;
use Bottel\Utils\DelayedResponse;
use Amp\Http\Client\Form;
use Amp\Http\Client\StreamedContent;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Bottel\Types\RawRequest;

class AmpHTTP extends RequestDriver {
    /**
     * WGet request
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
        }else{
            $url = config("TG_SCHEME", 'https') . "://$tghost:" . config("TG_PORT", 443) . "/bot{$this->token}/$method";
        }
        $client = HttpClientBuilder::buildDefault();
        $request = new Request($url);
        if(config("TG_METHOD") != 'GET'){
            $body = new Form;
            foreach($datas as $key => $value){
                if($value instanceof \CURLFile){
                    if($value->postname){
                        $body->addStream($key, StreamedContent::fromFile($value->name, $value->mime ?: null), $value->postname);
                    }else{
                        $body->addFile($key, $value->name, $value->mime ?: null);
                    }
                }else{
                    $body->addField($key, $value);
                }
            }
            $request->setBody($body);
        }
        $response = $client->request($request);
        return new DelayedResponse($response, $this);
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
        $client = HttpClientBuilder::buildDefault();
        $request = new Request($url);
        $response = $client->request($request);
        $res = $response->getBody()->buffer();
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
        $client = HttpClientBuilder::buildDefault();
        $request = new Request($url);
        $response = $client->request($request);
        $file = \Amp\File\openFile($into, 'w');
        $chunk = $response->getBody()->read();
        if($chunk == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            $this->last_error_code = 404;
            $this->last_error_message = "Not Found";
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
        $file->write($chunk);
        while(null !== $chunk = $response->getBody()->read()) {
            $file->write($chunk);
        }
        $file->close();
        return true;
    }
}
?>