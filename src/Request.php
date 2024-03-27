<?php
namespace Bottel;

use Amp\Future;
use Bottel\Request\AmpHTTP;
use Bottel\Request\Curl;
use Bottel\Request\Socket;
use Bottel\Request\WebhookReturn;
use Bottel\Request\WGet;
use Bottel\Request\Interpreter;
use Bottel\Types\CallbackQuery;
use Bottel\Types\RawRequest;
use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class Request {
    /**
     * @var int
     */
    const DRIVER_DEFAULT = 0;

    /**
     * @var int
     */
    const DRIVER_AMPHTTP = 1;

    /**
     * @var int
     */
    const DRIVER_CURL = 2;

    /**
     * @var int
     */
    const DRIVER_SOCKET = 3;

    /**
     * @var int
     */
    const DRIVER_WEBHOOK_RETURN = 4;

    /**
     * @var int
     */
    const DRIVER_WGET = 5;

    /**
     * @var int
     */
    public $driver;

    /**
     * @var CURL|WGet|Socket|WebhookReturn|AmpHTTP $connection
     */
    public $connection;

    /**
     * @var Bottel\Bottel
     */
    public $api;

    /**
     * @var CallbackQuery
     */
    public $auto_answer_to;

    /**
     * @var Future[]
     */
    public $futures = [];
    
    public static array $types_factory = [
        Types\WebhookInfo::class => [
            'getWebhookInfo'
        ],
        Types\User::class => [
            'getMe'
        ],
        Types\Message::class => [
            'sendMessage',
            'forwardMessage',
            'sendPhoto',
            'sendAudio',
            'sendDocument',
            'sendVideo',
            'sendAnimation',
            'sendVoice',
            'sendVideoNote',
            'sendLocation',
            'editMessageLiveLocation',
            'stopMessageLiveLocation',
            'sendVenue',
            'sendContact',
            'sendPoll',
            'sendDice',
            'editMessageText',
            'editMessageCaption',
            'editMessageMedia',
            'editMessageReplyMarkup',
            'sendSticker',
            'sendInvoice',
            'sendGame',
            'setGameScore',
        ],
        Types\MessageId::class => [
            'copyMessage'
        ],
        Types\UserProfilePhotos::class => [
            'getUserProfilePhotos',
        ],
        Types\File::class => [
            'getFile',
            'uploadStickerFile',
            'createNewStickerSet',
            'addStickerToSet',
        ],
        Types\ChatInviteLink::class => [
            'createChatInviteLink',
            'editChatInviteLink',
            'revokeChatInviteLink',
        ],
        Types\Chat::class => [
            'getChat',
        ],
        Types\ChatMember::class => [
            'getChatMember',
        ],
        Types\MenuButtonCommands::class => [
            'getChatMenuButton',
        ],
        Types\MenuButton::class => [
            'getChatMenuButton',
        ],
        Types\Poll::class => [
            'stopPoll',
        ],
        Types\StickerSet::class => [
            'getStickerSet',
        ],
        Types\SentWebAppMessage::class => [
            'answerWebAppQuery',
        ]
    ];
    
    /**
     * connect
     *
     * @param  string $token
     * @param  ?int $driver
     * @return bool
     */
    public function connect(string $token, int $driver = null): bool {
        # select default driver
        if(!$driver){
            if($this->driver){
                $driver = $this->driver;
            }elseif($this->api->async){
                $driver = self::DRIVER_AMPHTTP;
            }else{
                $driver = self::DRIVER_CURL;
            }
        }
        $this->driver = $driver;
        # intializing connection object
        switch($driver){
            case self::DRIVER_CURL:
                $this->connection = new Curl($token);
                break;
            case self::DRIVER_WGET:
                $this->connection = new WGet($token);
                break;
            case self::DRIVER_SOCKET:
                $this->connection = new Socket($token);
                break;
            case self::DRIVER_WEBHOOK_RETURN:
                $this->connection = new WebhookReturn($token);
                break;
            case self::DRIVER_AMPHTTP:
                $this->connection = new AmpHTTP($token);
                break;
            default:
                return false;
        }
        return true;
    }
    
    /**
     * __construct
     *
     * @param  \Bottel\Bottel $api
     * @param  string $token
     * @param  ?int $driver
     * @return void
     */
    public function __construct(\Bottel\Bottel $api, string $token, int $driver = null){
        $this->api($api);
        $this->connect($token, $driver);
    }
    
    /**
     * api
     *
     * @param  \Bottel\Bottel $api
     * @return void
     */
    public function api(\Bottel\Bottel $api){
        $this->api = $api;
        $update = $this->api->update();
        if($update && $update->callback_query){
            $this->auto_answer_to = $update->callback_query;
        }
    }

    /**
     * Change token
     *
     * @param  string $token
     * @return bool
     */
    public function token(string $token): bool {
        return $this->connect($token);
    }

    /**
     * Change driver
     *
     * @param  int $driver
     * @return bool
     */
    public function driver(int $driver): bool {
        return $this->connect($this->connection->token, $driver);
    }
    
    /**
     * request
     *
     * @param  string $method
     * @param  ?array $datas
     * @return DelayedJsonMapper|Future|bool
     */
    public function request(string $method, array $datas = []): DelayedJsonMapper|Future|bool {
        $datas = Interpreter::dejson($datas);
        $datas = Interpreter::applyDefaults($method, $datas, $this->api->defaults);
        $datas = Interpreter::serializeText($datas);
        $datas = Interpreter::flattenObjectFields($datas);
        $datas = Interpreter::handleFile($datas, $method, $this->api->symbolic);
        $datas = Interpreter::normalizeParseMode($datas);
        $datas = Interpreter::removeRedundantChatId($datas);
        $datas = Interpreter::jsonify($datas);
        if($this->auto_answer_to && Interpreter::callbackQueryAnswered($datas, $method, $this->auto_answer_to)){
            $this->auto_answer_to = null;
        }
        if($this->api->defaults->auto_action){
            $auto_action = Interpreter::prepareAutoAction($method, $datas);
            if($auto_action){
                $this->request($auto_action->method, $auto_action->datas);
            }
        }
        $callable = function()use($method, $datas){
            $result = $this->connection->request($method, $datas);
            if($this->driver == self::DRIVER_WEBHOOK_RETURN){
                $this->driver = null;
                $this->connect($this->connection->token);
            }
            if(!$result){
                return $result;
            }
            $method = strtolower($method);
            foreach(self::$types_factory as $type => $methods){
                foreach($methods as $value){
                    if(strtolower($value) == $method){
                        break 2;
                    }
                }
            }
            if(strtolower($value) != $method){
                return $result->fetch();
            }
            return new $type($result);
        };
        if($this->api->async){
            $future = \Amp\async($callable);
            $this->futures[] = $future;
            return $future;
        }else{
            return $callable();
        }
    }
    
    /**
     * file
     *
     * @param  string $path
     * @return ?string
     */
    public function file(string $path): ?string {
        return $this->connection->file($path);
    }
    
    /**
     * download
     *
     * @param  string $path
     * @param  string $into
     * @return bool
     */
    public function download(string $path, string $into): bool {
        return $this->connection->download($path, $into);
    }
    
    /**
     * await
     *
     * @return void
     */
    public function await() {
        if($this->connection instanceof Socket){
            $this->connection->wait();
        }
        foreach($this->futures as $future){
            $future->await();
        }
        $this->futures = [];
    }

    public function __destruct(){
        if($this->auto_answer_to && $this->api->defaults->auto_answer_callback){
            $result = $this->request('answerCallbackQuery', [
                'callback_query_id' => $this->auto_answer_to->id,
            ]);
            if($result instanceof Future){
                $result->await();
            }
        }
        $this->await();
    }
    
    /**
     * error
     *
     * @return string
     */
    public function error(): string {
        return $this->connection->error();
    }
    
    /**
     * errno
     *
     * @return int
     */
    public function errno(): int {
        return $this->connection->errno();
    }
    
    /**
     * last
     *
     * @return ?RawRequest
     */
    public function last(): ?RawRequest {
        return $this->connection->last();
    }
    
    /**
     * success
     *
     * @return bool
     */
    public function success(): bool {
        return $this->connection->success();
    }
}