<?php
namespace Bottel;

use Amp\Future;
use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class Statement {    
    /**
     * @var array
     */
    public $arguments = [];
    
    /**
     * @var string
     */
    public $method;

    /**
     * @var \Bottel\Request
     */
    public $api;
    
    /**
     * @var false
     */
    public $sended = false;

    public function __construct(\Bottel\Request $api){
        $this->api($api);
    }

    /**
     * @param  \Bottel\Request $api
     * @return $this
     */
    public function api(\Bottel\Request $api): Statement {
        $this->api = $api;
        return $this;
    }
    
    /**
     * @param  string $name
     * @param  ?array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments = []): Statement {
        $this->method = $name;
        $this->arguments = $arguments;
        $this->sended = false;
        return $this;
    }
    
    /**
     * @param  mixed $arguments
     * @return DelayedJsonMapper|Future|false
     */
    public function send(array $arguments = []): DelayedJsonMapper|Future|false {
        $datas = array_merge($this->arguments, $arguments);
        $response = $this->api->request($this->method, $datas);
        $this->sended = true;
        return $response;
    }
    
    /**
     * @return void
     */
    public function cancel(){
        $this->sended = true;
    }
    
    /**
     * @return DelayedJsonMapper|false
     */
    public function await(): DelayedJsonMapper|false {
        $response = $this->send();
        if($response instanceof Future){
            $response = $response->await();
        }
        return $response;
    }
    
    /**
     * @return void
     */
    public function __destruct(){
        if(!$this->sended){
            $this->await();
        }
    }
    
    /**
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set(string $key, $value){
        $this->arguments[$key] = $value;
    }
    
    /**
     * @param  string $key
     * @return void
     */
    public function __get(string $key){
        return $this->arguments[$key] ?? null;
    }
    
    /**
     * @param  string $key
     * @return void
     */
    public function __isset(string $key){
        return isset($this->arguments[$key]);
    }
    
    /**
     * @param  string $key
     * @return void
     */
    public function __unset(string $key){
        unset($this->arguments[$key]);
    }

    /**
     * @param  string $text
     * @return void
     */
    public function textTop(string $text){
        if(isset($this->text)){
            $text = $text . "\n" . $this->text;
        }
        $this->text = $text;
    }

    /**
     * @param  string $text
     * @return void
     */
    public function textBottom(string $text){
        if(isset($this->text)){
            $text = $this->text . "\n" . $text;
        }
        $this->text;
    }
    
    /**
     * @param  array $keyboard
     * @return void
     */
    public function keyboard(array $keyboard){
        $this->reply_markup = [
            "keyboard" => $keyboard,
        ];
    }
    
    /**
     * @param  ?array $keyboard
     * @return void
     */
    public function inlineKeyboard(array $keyboard = []){
        $this->reply_markup = [
            "inline_keyboard" => $keyboard,
        ];
    }
}