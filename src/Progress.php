<?php
namespace Bottel;

use Bottel\Statement\EditTextStatement;
use Bottel\Response\TextResponse;

class Progress {
    /**
     * @var array $lists
     */
    public static $lists = [
        'two-square' => ['◻️', '◼️'],
        'filled' => [' ', '▏', '▎', '▌', '▋', '▊'],
        'square' => ['⬜️', '◻️', '◽️', '▫️', '▪️', '◾️', '◼️' ,'⬛️'],
        'decimal' => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
        'parallelogram' => ['▱', '▰'],
        'shades' => ['█', '▓', '▒', '░'],
        'whiteshades' => [' ', '░', '▒', '▓', '█'],
        'circle' => ['◯', '⬤'],
        'dots' => ['⣀', '⣄', '⣤', '⣦', '⣶', '⣷', '⣿'],
        'volume' => ['▁', '▂', '▃', '▄', '▅', '▆', '▇']
    ];

    private static $barlength = [
        'two-square' => 10,
        'filled' => 20,
        'square' => 10,
        'decimal' => 10,
        'parallelogram' => 10,
        'shades' => 20,
        'whiteshades' => 20,
        'circle' => 10,
        'dots' => 10,
        'volume' => 20,
    ];

    private static $barformat = [
        'two-square' => "%bar",
        'filled' => "`[%bar]`",
        'square' => "%bar",
        "decimal" => "%bar",
        "parallelogram" => "%bar",
        "shades" => "`%bar`",
        "whiteshades" => "`[%bar]`",
        'circle' => "%bar",
        'dots' => "%bar",
        'volume' => "`%bar`",
    ];
    
    /**
     * make
     *
     * @param  array|string $list
     * @param  int $progress
     * @param  int $end
     * @param  int $length
     * @param  string $join
     * @return string
     */
    public static function make(array|string $list, int $progress, int $end = 100, int $length = 10, string $join = ''): string {
        if(is_string($list)){
            if(isset(self::$lists[$list])){
                $list = self::$lists[$list];
            }else{
                return false;
            }
        }
        $count = count($list);
        $rational = $progress / $end * $length;
        $filled = floor($rational);
        $middle = floor($rational * $count) - $filled * $count;
        $empty = $length - $filled - 1;
        $filled = $filled == 0 ? '' : str_repeat($list[$count-1], $filled);
        $middle = $progress == $end ? '' : $list[$middle];
        $empty = $empty <= 0 ? '' : str_repeat($list[0], $empty);
        return "$filled$middle$join$empty";
    }

    /**
     * @var int $chat_id
     */
    public $chat_id;
        
    /**
     * @var \Bottel\Api $api
     */
    public $api;
        
    /**
     * @var array $list
     */
    public $list;
        
    /**
     * @var int $sizee
     */
    public $size;
        
    /**
     * @var string $joib
     */
    public $join = '';
        
    /**
     * @var int $length
     */
    public $length = 10;
        
    /**
     * @var string $parse_mode
     */
    public $parse_mode = 'MarkDown';
        
    /**
     * @var array|string $reply_markup
     */
    public $reply_markup = null;
    
    /**
     * @var int $reply_to_message_id
     */
    public $reply_to_message_id = null;
    
    /**
     * @var string $format
     */
    public $format;
    
    /**
     * @var int $start_time
     */
    public $start_time;
        
    /**
     * @var int $progress
     */
    public $progress = -1;
        
    /**
     * @var ?TextResponse $message
     */
    public $message;
        
    /**
     * @var int $precision
     */
    public $precision = 1;
        
    /**
     * @var array $successes
     */
    public $successes = [];
        
    /**
     * @var string $working
     */
    public $working = '';
        
    /**
     * @var string $beforesuccess
     */
    public $beforesuccess = "✅ ";
        
    /**
     * @var string $beforeworking
     */
    public $beforeworking = "⚙️ ";
        
    /**
     * @var bool $await
     */
    public $await = false;
    
    /**
     * __construct
     *
     * @param  \Bottel\Api $api
     * @param  array|string $list
     * @param  int $size
     * @return void
     */
    public function __construct(\Bottel\Bottel $api, array|string $list, int $size){
        $format = is_string($list) && isset(self::$barformat[$list]) ? self::$barformat[$list] : "%bar";
        $this->length = is_string($list) && isset(self::$barlength[$list]) ? self::$barlength[$list] : 10;
        $format.= " %percent%%\n";
        $format.= "\[%progress/%size] %left remaining\n";
        $format.= "%success\n";
        $format.= "%working";
        $this->api = $api;
        $this->list = $list;
        $this->size = $size;
        $this->format = $format;
    }
    
    /**
     * beforeSuccess
     *
     * @param  string $before
     * @return Progress
     */
    public function beforeSuccess(string $before): Progress {
        $this->beforesuccess = $before;
        return $this;
    }
    
    /**
     * beforeWorking
     *
     * @param  string $before
     * @return Progress
     */
    public function beforeWorking(string $before): Progress {
        $this->beforeworking = $before;
        return $this;
    }
    
    /**
     * join
     *
     * @param  ?string $join
     * @return Progress
     */
    public function join(string $join = ''): Progress {
        $this->join = $join;
        return $this;
    }
    
    /**
     * precision
     *
     * @param  ?int $precision
     * @return Progress
     */
    public function precision(int $precision = 1): Progress {
        $this->precision = $precision;
        return $this;
    }
    
    /**
     * format
     *
     * @param  string $format
     * @return Progress
     */
    public function format(string $format): Progress {
        $this->format = $format;
        return $this;
    }
    
    /**
     * length
     *
     * @param  ?int $length
     * @return Progress
     */
    public function length(int $length = 10): Progress {
        $this->length = $length;
        return $this;
    }
    
    /**
     * size
     *
     * @param  int $size
     * @return Progress
     */
    public function size(int $size): Progress {
        $this->size = $size;
        return $this;
    }
    
    /**
     * list
     *
     * @param  array|string $list
     * @return Progress
     */
    public function list(array|string $list): Progress {
        $this->list = $list;
        return $this;
    }
    
    /**
     * await
     *
     * @param  ?bool $await
     * @return Progress
     */
    public function await(bool $await = true): Progress {
        $this->await = $await;
        return $this;
    }
    
    /**
     * chat
     *
     * @param  ?int $chat_id
     * @return Progress
     */
    public function chat(int $chat_id = null): Progress {
        $this->chat_id = $chat_id;
        return $this;
    }
    
    /**
     * parse
     *
     * @param  ?string $parse_mode
     * @return Progress
     */
    public function parse(string $parse_mode = null): Progress {
        $this->parse_mode = $parse_mode;
        return $this;
    }
    
    /**
     * markup
     *
     * @param  string|array|null $reply_markup
     * @return Progress
     */
    public function markup($reply_markup = null): Progress {
        $this->reply_markup = $reply_markup;
        return $this;
    }
    
    /**
     * keyboard
     *
     * @param  array $keyboard
     * @param  ?bool $resize
     * @return Progress
     */
    public function keyboard(array $keyboard, bool $resize = null): Progress {
        return $this->markup($resize === null ? [
            'keyboard' => $keyboard,
        ] : [
            'keyboard' => $keyboard,
            'resize_keyboard' => $resize,
        ]);
    }
    
    /**
     * inlineKeyboard
     *
     * @param  array $keyboard
     * @return Progress
     */
    public function inlineKeyboard(array $keyboard): Progress {
        return $this->markup([
            'inline_keyboard' => $keyboard
        ]);
    }
    
    /**
     * reply
     *
     * @param  ?mixed $message_id
     * @return Progress
     */
    public function reply($message_id = null): Progress {
        $this->reply_to_message_id = $message_id;
        return $this;
    }
    
    /**
     * readableTime
     *
     * @param  int $seconds
     * @return string
     */
    private function readableTime(int $seconds): string {
        $days = floor($seconds / 86400);
        $seconds %= 86400;
        $hours = floor($seconds / 3600);
        $seconds %= 3600;
        $minutes = floor($seconds / 60);
        $seconds %= 60;
        $result = '';
        if($days > 0)
            $result .= "$days day".($days > 1 ? 's' : '').', ';
        if($hours > 0)
            $result .= "$hours hour".($hours > 1 ? 's' : '').', ';
        if($minutes > 0)
            $result .= "$minutes minute".($minutes > 1 ? 's' : '').', ';
        $result .= "$seconds second".($seconds > 1 ? 's' : '');
        return $result;
    }
    
    /**
     * start
     *
     * @param  ?string $working
     * @return void
     */
    public function start(string $working = ''): void {
        $this->working = $working ? ($this->beforeworking ?: '') . $working : '';
        $this->progress = 0;
        $this->start_time = microtime(true);
        $this->progress();
    }
    
    /**
     * end
     *
     * @param  ?string $working
     * @return void
     */
    public function end(string $working = ''): void {
        $this->working = $working ? ($this->beforeworking ?: '') . $working : '';
        $this->progress = $this->size;
        $this->progress();
    }
    
    /**
     * success
     *
     * @param  string $success
     * @return Progress
     */
    public function success(string $success): Progress {
        $this->successes[] = "\n" . ($this->beforesuccess ?: '') . $success;
        return $this;
    }
    
    /**
     * progress
     *
     * @return void
     */
    public function progress(): void {
        $bar = self::make($this->list, $this->progress, $this->size, $this->length, $this->join);
        $percent = round($this->progress / $this->size * 100);
        $now = microtime(true);
        $past = $this->progress == 0 || $now - $this->start_time <= 1e-5 ? '?' : microtime(true) - $this->start_time;
        if($past == '?'){
            $rate = $rateinv = '?';
            $past = $left = '?';
        }else{
            $rate = round($past / $this->progress, $this->precision);
            $rateinv = round($this->progress / $past, $this->precision);
            $left = round($past / $this->progress * ($this->size - $this->progress));
            $past = round($past);
            $left = $this->readableTime($left);
            $past = $this->readableTime($past);
        }
        $working = $this->working;
        $successes = implode('', $this->successes);
        $format = value($this->format);
        $result = strtr($format, [
            "%%" => "%",
            "%progress" => $this->progress,
            "%size" => $this->size,
            "%bar" => $bar,
            "%percent" => $percent,
            "%past" => $past,
            "%left" => $left,
            "%success" => $successes,
            "%working" => $working,
            "%rate" => $rate,
            "%rateinv" => $rateinv,
        ]);
        if($this->message){
            $res = $this->message->edit($result)
                                 ->parse($this->parse_mode)
                                 ->markup($this->reply_markup);
        }else{
            $this->message = $this->api
                             ->text($result)
                             ->chat($this->chat_id)
                             ->parse($this->parse_mode)
                             ->markup($this->reply_markup)
                             ->reply($this->reply_to_message_id)
                             ->send();
        }
        if($this->await){
            $this->api->await();
        }
    }
    
    /**
     * iterate
     *
     * @param  ?string $working
     * @return void
     */
    public function iterate(string $working = ''): void {
        $this->working = $working ? ($this->beforeworking ?: '') . $working : '';
        if(!$this->start_time){
            $this->start_time = microtime(true);
        }
        ++$this->progress;
        $this->progress();
    }
    
    /**
     * working
     *
     * @param  ?string $working
     * @return Progress
     */
    public function working(string $working): Progress  {
        if($this->progress >= 0){
            $this->working = ($this->beforeworking ?: '') . $working;
            $this->progress();
        }
        return $this;
    }
    
    /**
     * edit
     *
     * @param  string $text
     * @return MessageStatement
     */
    public function edit(string $text): EditTextStatement {
        return $this->message->edit($text);
    }
}