<?php
namespace Bottel\Utils;

use Amp\Http\Client\Response;
use Bottel\Request\Socket;
use Bottel\Request\AmpHTTP;

class DelayedResponse {
    /**
     * result
     *
     * @var array|bool|resource|Response|null
     */
    public $response = null;

    /**
     * @var Socket|AmpHTTP|null
     */
    private $driver;
    
    /**
     * __construct
     *
     * @param  mixed $response
     * @param  mixed $driver
     * @return void
     */
    public function __construct(mixed $response, mixed $driver = null){
        $this->response = $response;
        $this->driver = $driver;
    }
    
    /**
     * wait
     *
     * @return bool
     */
    public function wait(): bool {
        if($this->driver instanceof Socket){
            if(!feof($this->response)){
                fgetc($this->response);
                return true;
            }
        }
    }
    
    /**
     * close
     *
     * @return bool
     */
    public function close(): bool {
        if($this->driver instanceof Socket){
            if(!feof($this->response)){
                fclose($this->response);
                return true;
            }
        }
    }

    private function readSocket(): array|bool {
        $header = '';
        do {
            $header .= $packet = fread($this->response, 16);
        }while(strpos($header, "\r\n\r\n") === false && strlen($packet) == 16);
        $header = explode("\r\n\r\n", $header, 2);
        $response = $header[1];
        $header = $header[0];
        preg_match('/Content-Length: (\d+)/i', $header, $match);
        if($match[1] != 0){
            $response .= fread($this->response, $match[1] - strlen($response));
        }
        return $response;
    }

    private function readAmpHTTP(): array|bool {
        $response = $this->response->getBody()->buffer();
        return $response;
    }

    public function fetch(): array|bool {
        if($this->driver instanceof Socket){
            $response = $this->readSocket();
        }elseif($this->driver instanceof AmpHTTP){
            $response = $this->readAmpHTTP();
        }else{
            return $this->response;
        }
        if(!$response){
            return false;
        }
        $response = json_decode($response, true);
        if($this->driver){
            if(!isset($response['ok']) || !$response['ok']){
                $this->driver->last_error_code = $response['error_code'] ?? 100;
                $this->driver->last_error_message = $response['description'] ?? 'Unknown error';
                return false;
            }
            $this->driver->last_error_code = 200;
            $this->driver->last_error_message = '';
        }
        return $response['result'];
    }

        
    /**
     * error
     *
     * @return string
     */
    public function error(): string {
        return $this->driver->last_error_message;
    }
        
    /**
     * errno
     *
     * @return int
     */
    public function errno(): int {
        return $this->driver->last_error_code;
    }
    
    /**
     * sccess
     *
     * @return bool
     */
    public function success(): bool {
        return $this->driver->last_error_code == 200;
    }
    
    /**
     * last
     *
     * @return ?array
     */
    public function last(): ?array {
        return $this->driver->last_request;
    }
}

?>