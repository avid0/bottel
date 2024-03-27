<?php
namespace Bottel\Traits;

trait ValidationCallbacks {    
    /**
     * @var Update
     */
    public $update;    
    /**
     * @var \Types\Message
     */
    public $message;    
    /**
     * @var \Bottel\Bottel
     */
    public $api;
    
    /**
     * message
     *
     * @param  ?string $type
     * @return bool
     */
    public function message(string $type = null): bool {
        return $this->update->type == 'message' && (!$type || $this->update->message->type == $type);
    }
    
    /**
     * command
     *
     * @param  ?string $prefix
     * @param  ?string $command
     * @return array|string|bool
     */
    public function command(string $prefix = '/', string $command = null): array|string|bool {
        if(!$this->text()){
            return false;
        }
        $text = $this->message->text;
        if($prefix){
            $prefixreg = preg_quote($prefix, '/');
            $regex = "/^[{$prefixreg}]([a-zA-Z0-9_]{1,32})((?:\s.+){0,1})$/us";
        }else{
            $regex = "/^([a-zA-Z0-9_]{1,32})((?:\s.+){0,1})$/us";
        }
        if(!preg_match($regex, $text, $matches))
            return false;
        $matches[2] = ltrim($matches[2]);
        if(!$command)
            return array($matches[1], $matches[2]);
        if(strtolower($matches[1]) != strtolower($command))
            return false;
        if($matches[2] === '')
            return true;
        return $matches[2];
    }
    
    /**
     * startWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function startWith(string $needle): string|bool {
        if(!$this->text()){
            return false;
        }
        $text = $this->message->text;
        if(strpos($text, $needle) === 0){
            return substr($text, strlen($needle));
        }
        return false;
    }
    
    /**
     * startiWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function startiWith(string $needle): string|bool {
        if(!$this->text()){
            return false;
        }
        $text = $this->message->text;
        if(stripos($text, $needle) === 0){
            return substr($text, strlen($needle));
        }
        return false;
    }
    
    /**
     * endWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function endWith(string $needle): string|bool {
        if(!$this->text()){
            return false;
        }
        $text = $this->message->text;
        if(substr($text, -strlen($needle)) == $needle){
            return substr($text, 0, strlen($needle));
        }
        return false;
    }
    
    /**
     * endiWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function endiWith(string $needle): string|bool {
        if(!$this->text()){
            return false;
        }
        $text = $this->message->text;
        if(strtolower(substr($text, -strlen($needle))) == strtolower($needle)){
            return substr($text, 0, strlen($needle));
        }
        return false;
    }

    /**
     * queryStartWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function callbackStartWith(string $needle): string|bool {
        if(!$this->callback()){
            return false;
        }
        $data = $this->update->callback_query->data;
        if(strpos($data, $needle) === 0){
            return substr($data, strlen($needle));
        }
        return false;
    }
    
    /**
     * callbackStartiWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function callbackStartiWith(string $needle): string|bool {
        if(!$this->callback()){
            return false;
        }
        $data = $this->update->callback_query->data;
        if(stripos($data, $needle) === 0){
            return substr($data, strlen($needle));
        }
        return false;
    }
    
    /**
     * callbackEndWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function callbackEndWith(string $needle): string|bool {
        if(!$this->callback()){
            return false;
        }
        $data = $this->update->callback_query->data;
        if(substr($data, -strlen($needle)) == $needle){
            return substr($data, 0, strlen($needle));
        }
        return false;
    }
    
    /**
     * callbackEndiWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function callbackEndiWith(string $needle): string|bool {
        if(!$this->callback()){
            return false;
        }
        $data = $this->update->callback_query->data;
        if(strtolower(substr($data, -strlen($needle))) == strtolower($needle)){
            return substr($data, 0, strlen($needle));
        }
        return false;
    }
    
    /**
     * start
     *
     * @return string|bool
     */
    public function start(): string|bool {
        return $this->command('/', 'start');
    }
    
    /**
     * pregText
     *
     * @param  string $pattern
     * @param  ?int $flags
     * @param  ?int $offset
     * @return array|bool
     */
    public function pregText(string $pattern, int $flags = 0, int $offset = 0): array|bool {
        if(!$this->message()){
            return false;
        }
        if(isset($this->message->text))
            $text = $this->message->text;
        elseif(isset($this->message->caption))
            $text = $this->message->caption;
        else return false;
        return preg_match($pattern, $text, $matches, $flags, $offset) ? $matches : false;
    }
    
    /**
     * pregTextAll
     *
     * @param  string $pattern
     * @param  ?int $flags
     * @param  ?int $offset
     * @return array|bool
     */
    public function pregTextAll(string $pattern, int $flags = 0, int $offset = 0): array|bool {
        if(!$this->message()){
            return false;
        }
        if(isset($this->message->text))
            $text = $this->message->text;
        elseif(isset($this->message->caption))
            $text = $this->message->caption;
        else return false;
        return preg_match_all($pattern, $text, $matches, $flags, $offset) ? $matches : false;
    }
        
    /**
     * photo
     *
     * @return bool
     */
    public function photo(): bool {
        return $this->update->type == 'message' && isset($this->message->photo);
    }
    
    /**
     * document
     *
     * @return bool
     */
    public function document(): bool {
        return $this->update->type == 'message' && isset($this->message->document);
    }
    
    /**
     * video
     *
     * @return bool
     */
    public function video(): bool {
        return $this->update->type == 'message' && isset($this->message->video);
    }
    
    /**
     * voice
     *
     * @return bool
     */
    public function voice(): bool {
        return $this->update->type == 'message' && isset($this->message->voice);
    }
    
    /**
     * videoNote
     *
     * @return bool
     */
    public function videoNote(): bool {
        return $this->update->type == 'message' && isset($this->message->video_note);
    }
    
    /**
     * sticker
     *
     * @return bool
     */
    public function sticker(): bool {
        return $this->update->type == 'message' && isset($this->message->sticker);
    }
    
    /**
     * venue
     *
     * @return bool
     */
    public function venue(): bool {
        return $this->update->type == 'message' && isset($this->message->venue);
    }
    
    /**
     * location
     *
     * @return bool
     */
    public function location(): bool {
        return $this->update->type == 'message' && isset($this->message->location);
    }
    
    /**
     * dice
     *
     * @param  ?string $emoji
     * @param  ?int $value
     * @return bool
     */
    public function dice(string $emoji = null, int $value = null): bool {
        return $this->update->type == 'message' && isset($this->message->dice) && (!$emoji || $this->message->dice->emoji == $emoji)
            && (!$emoji || $value === null || $this->message->dice->value == $value);
    }
    
    /**
     * audio
     *
     * @return bool
     */
    public function audio(): bool {
        return $this->update->type == 'message' && isset($this->message->audio);
    }
    
    /**
     * animation
     *
     * @return bool
     */
    public function animation(): bool {
        return $this->update->type == 'message' && isset($this->message->animation);
    }
    
    /**
     * poll
     *
     * @return bool
     */
    public function poll(): bool {
        return $this->update->type == 'message' && isset($this->message->poll);
    }
    
    /**
     * text
     *
     * @param  mixed $text
     * @return bool
     */
    public function text(string $text = null): bool {
        return $this->update->type == 'message' && isset($this->message->text) && (!$text || $this->message->text == $text);
    }
    
    /**
     * filterText
     *
     * @param  int $filter
     * @return bool
     */
    public function filterText(int $filter): bool {
        return $this->text() && filter_var($this->message->text, $filter);
    }
    
    /**
     * minText
     *
     * @param  int $length
     * @return bool
     */
    public function minText(int $length): bool {
        return $this->text() && strlen($this->message->text) >= $length;
    }
    
    /**
     * maxText
     *
     * @param  int $length
     * @return bool
     */
    public function maxText(int $length): bool {
        return $this->text() && strlen($this->message->text) <= $length;
    }
        
    /**
     * texti
     *
     * @param  string $text
     * @return bool
     */
    public function texti(string $text): bool {
        return $this->update->type == 'message' && isset($this->message->text) && strtolower($this->message->text) == strtolower($text);
    }
    
    /**
     * numeric
     *
     * @return bool
     */
    public function numeric(): bool {
        return $this->update->type == 'message' && isset($this->message->text) && is_numeric($this->message->text);
    }
    
    /**
     * callback
     *
     * @param  mixed $data
     * @return bool
     */
    public function callback(string $data = null): bool {
        return isset($this->update->callback_query) && (!$data || $this->update->callback_query->data == $data);
    }
        
    /**
     * pregCallback
     *
     * @param  string $pattern
     * @param  ?int $flags
     * @param  ?int $offset
     * @return array|bool
     */
    public function pregCallback(string $pattern, int $flags = 0, int $offset = 0): array|bool {
        if(!$this->callback())
            return false;
        $data = $this->update->callback_query->data;
        return preg_match($pattern, $data, $matches, $flags, $offset) ? $matches : false;
    }
    
    /**
     * pregCallbackAll
     *
     * @param  string $pattern
     * @param  ?int $flags
     * @param  ?int $offset
     * @return array|bool
     */
    public function pregCallbackAll(string $pattern, int $flags = 0, int $offset = 0): array|bool {
        if(!$this->callback())
            return false;
        $data = $this->update->callback_query->data;
        return preg_match_all($pattern, $data, $matches, $flags, $offset) ? $matches : false;
    }
    
    /**
     * group
     *
     * @return bool
     */
    public function group(): bool {
        return $this->update->chat && in_array($this->update->chat->type, ['group', 'supergroup']);
    }
    
    /**
     * pirvate
     *
     * @return bool
     */
    public function pirvate(): bool {
        return $this->update->chat && $this->update->chat->type == 'private';
    }
    
    /**
     * channel
     *
     * @return bool
     */
    public function channel(): bool {
        return $this->update->chat && $this->update->chat->type == 'channel';
    }
    
    /**
     * replyed
     *
     * @return bool
     */
    public function replyed(): bool {
        return isset($this->message->reply) && $this->api->botId() == $this->message->reply->from->id;
    }
    
    /**
     * joined
     *
     * @return bool
     */
    public function joined(): bool {
        return isset($this->message->new_chat_members) && in_array($this->api->botId(), array_column($this->message->new_chat_members, 'id'));
    }
    
    /**
     * owner
     *
     * @return bool
     */
    public function owner(): bool {
        return $this->api->user && $this->api->owner && $this->api->user->id == $this->api->owner;
    }
    
    /**
     * chatMember
     *
     * @param  ?string $status
     * @return bool
     */
    public function chatMember(string $status = null): bool {
        return isset($this->update->chat_member) && (!$status || $this->update->chat_member->new_chat_member->status == $status);
    }
        
    /**
     * left
     *
     * @return bool
     */
    public function left(): bool {
        return isset($this->update->chat_member) && $this->update->chat_member->new_chat_member->status == 'left';
    }
    
    /**
     * inline
     *
     * @param  ?string $query
     * @return bool
     */
    public function inline(string $query = null): bool {
        return isset($this->update->inline_query->query) && (!$query || $this->update->inline_query->query == $query);
    }

    /**
     * pregInline
     *
     * @param  string $pattern
     * @param  ?int $flags
     * @param  ?int $offset
     * @return array|bool
     */
    public function pregInline(string $pattern, int $flags = 0, int $offset = 0): array|bool {
        if(!$this->inline())
            return false;
        $query = $this->update->inline_query->query;
        return preg_match($pattern, $query, $matches, $flags, $offset) ? $matches : false;
    }
        
    /**
     * globalPregInlineAll
     *
     * @param  string $pattern
     * @param  ?int $flags
     * @param  ?int $offset
     * @return array|bool
     */
    public function globalPregInlineAll(string $pattern, int $flags = 0, int $offset = 0): array|bool {
        if(!$this->inline())
            return false;
        $query = $this->update->inline_query->query;
        return preg_match_all($pattern, $query, $matches, $flags, $offset) ? $matches : false;
    }
    
    /**
     * inlineStartWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function inlineStartWith(string $needle): string|bool {
        if(!$this->inline())
            return false;
        $query = $this->update->inline_query->query;
        if(strpos($query, $needle) === 0){
            return substr($query, strlen($needle));
        }
        return false;
    }
    
    /**
     * inlineStartiWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function inlineStartiWith(string $needle): string|bool {
        if(!$this->inline())
            return false;
        $query = $this->update->inline_query->query;
        if(stripos($query, $needle) === 0){
            return substr($query, strlen($needle));
        }
        return false;
    }
    
    /**
     * inlineEndWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function inlineEndWith(string $needle): string|bool {
        if(!$this->inline())
            return false;
        $query = $this->update->inline_query->query;
        if(substr($query, -strlen($needle)) == $needle){
            return substr($query, 0, strlen($needle));
        }
        return false;
    }
    
    /**
     * inlineEndiWith
     *
     * @param  string $needle
     * @return string|bool
     */
    public function inlineEndiWith(string $needle): string|bool {
        if(!$this->inline())
            return false;
        $query = $this->update->inline_query->query;
        if(strtolower(substr($query, -strlen($needle))) == strtolower($needle)){
            return substr($query, 0, strlen($needle));
        }
        return false;
    }
    
    /**
     * chosenInline
     *
     * @param  ?string $query
     * @return bool
     */
    public function chosenInline(string $query = null): bool {
        return isset($this->update->chosen_inline_result) && (!$query || $this->update->chosen_inline_result->query == $query);
    }
}