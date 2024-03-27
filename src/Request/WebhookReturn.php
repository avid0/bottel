<?php
/**
 * @author Avid
 * 
 * WebhookReturn API Request Method
 */
namespace Bottel\Request;

use Bottel\Types\RawRequest;
use Bottel\Utils\DelayedResponse;
use Bottel\Webhook;

class WebhookReturn extends RequestDriver {    
    /**
     * @var string
     */
    public $token;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(string $token = null){
        $this->token = $token;
    }

    /**
     * WebhookReturn request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return DelayedResponse
     */
    public function request(string $method, array $datas = []): DelayedResponse {
        $this->last_request = new RawRequest($method, $datas);

        $datas['method'] = $method;
        $datas = json_encode($datas);
        $result = Webhook::close($datas);
        return new DelayedResponse($result);
    }
}
?>