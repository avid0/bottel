<?php
namespace Bottel\Request;

use Bottel\Types\RawRequest;

class RequestDriver {
    /**
     * Bot API Token
     * @var string $token
     */
    public $token;

    /**
     * Last error code
     * @var int $last_error_code
     */
    public $last_error_code = 200;

    /**
     * Last error message
     * @var string $last_error_message
     */
    public $last_error_message = '';

    /**
     * Last request information
     * @var ?RawRequest $last_request
     */
    public $last_request;

    /**
     * WGet Method initializer
     * 
     * @method __construct
     * @param string $token
     */
    public function __construct(string $token){
        $this->token = $token;
    }
    
    /**
     * error
     *
     * @return string
     */
    public function error(): string {
        return $this->last_error_message;
    }
        
    /**
     * errno
     *
     * @return int
     */
    public function errno(): int {
        return $this->last_error_code;
    }
    
    /**
     * sccess
     *
     * @return bool
     */
    public function success(): bool {
        return $this->last_error_code == 200;
    }
    
    /**
     * last
     *
     * @return ?RawRequest
     */
    public function last(): ?RawRequest {
        return $this->last_request;
    }
}

?>