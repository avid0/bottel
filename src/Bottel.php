<?php
namespace Bottel;

class Bottel {    
    /**
     * @var Request
     */
    public $api;
    
    /**
     * @var bool
     */
    public $async = false;

    /**
     * @var Update
     */
    public $update;

    /**
     * @var Types\Message
     */
    public $message;

    /**
     * @var Types\Chat
     */
    public $chat;

    /**
     * @var Types\User
     */
    public $user;

    /**
     * @var int
     */
    public $owner;

    /**
     * @var Types\CallbackQuery
     */
    public $callback_query;

    /**
     * @var Types\InlineQuery
     */
    public $inline_query;

    /**
     * @var string
     */
    public $inline_message_id;

    /**
     * @var Defaults
     */
    public $defaults;

    /**
     * @var Keyboard
     */
    public $keyboard;

    /**
     * @var Symbolic
     */
    public $symbolic;

    /**
     * @var Validation
     */
    public $validation;

    /**
     * @var Statement
     */
    public $statement;

    /**
     * __construct
     *
     * @param  ?string $token
     * @param  ?int $driver
     * @return void
     */
    public function __construct(string $token = null, int $driver = null){
        $token ??= config("BOT_TOKEN");
        $driver ??= config("BOT_DRIVER");
        $this->api = new Request($this, $token, $driver);
        $this->symbolic = new Symbolic;
        $this->defaults = new Defaults;
        $this->keyboard = new Keyboard;
        $this->validation = new Validation($this);
        $this->statement = new Statement($this->api);
    }
    
    /**
     * update
     *
     * @param  ?array $update
     * @return ?Update
     */
    public function update(array $update = null): ?Update {
        if(!$update){
            if($this->update){
                return $this->update;
            }
            $update = Webhook::update();
        }
        if(!$update){
            return null;
        }
        $this->update = new Update($update);
        $this->message = $this->update->recursive_message;
        $this->chat = $this->update->chat;
        $this->user = $this->update->user;
        $this->callback_query = $this->update->callback_query;
        $this->inline_query = $this->update->inline_query;
        $this->inline_message_id = $this->update->inline_message_id;
        return $this->update;
    }
        
    /**
     * tokan
     *
     * @param  string $token
     * @return Bottel
     */
    public function tokan(string $token): Bottel {
        $this->api->token($token);
        return $this;
    }
    
    /**
     * driver
     *
     * @param  int $driver
     * @return Bottel
     */
    public function driver(int $driver): Bottel {
        $this->api->driver($driver);
        return $this;
    }
    
    /**
     * botId
     *
     * @return int|false
     */
    public function botId(): int|false {
        if(!$this->api->connection){
            return false;
        }
        $botid = explode(':', $this->api->connection->token, 2);
        $botid = $botid[0];
        if($botid){
            return $botid;
        }
        return false;
    }
        
    /**
     * dateout
     *
     * @param  int $seconds
     * @param  ?callable $callable
     * @return void
     */
    public function dateout(int $seconds, $callable = null){
        if(isset($this->update->date) && time() - $this->update->date > $seconds){
            if(is_callable($callable)){
                $callable();
            }
            exit;
        }
    }
    
    /**
     * async
     *
     * @param  ?bool $async
     * @return Bottel
     */
    public function async(bool $async = true): Bottel {
        if($async == false){
            $this->api->await();
        }
        $this->async = $async;
        return $this;
    }
    
    /**
     * await
     *
     * @return Bottel
     */
    public function await(): Bottel {
        $this->api->await();
        return $this;
    }
}
