<?php
namespace Bottel;

use Bottel\Traits\ValidationCallbacks;

class Validation {
    use ValidationCallbacks;
    
    /**
     * @var array
     */
    protected static $conditions;

    public function __construct(\Bottel\Bottel $api){
        $this->api($api);
    }

    public function api(\Bottel\Bottel $api) {
        $this->api = $api;
        $this->update = $api->update();
        $this->message = $api->message;
    }
        
    /**
     * register
     *
     * @param  string $key
     * @param  callable $validator
     * @param  ?callable $unsuccess
     * @param  ?array $settings
     * @return void
     */
    public static function register(string $key, $validator, $unsuccess = null, array $settings = []){
        self::$conditions[strtolower($key)] = [$validator, $unsuccess, $settings];
    }
    
    /**
     * registerAll
     *
     * @param  array $registers
     * @return void
     */
    public static function registerAll(array $registers){
        foreach($registers as $key => $value){
            if(is_array($value)){
                $value = array_values($value);
                if(count($value) == 1)
                    $value[] = null;
                if(count($value) == 2)
                    $value[] = null;
                if(count($value) == 3)
                    self::$conditions[strtolower($key)] = $value;
            }else{
                self::$conditions[strtolower($key)] = [$value, null, null];
            }
        }
    }
    
    /**
     * unregister
     *
     * @param  string $key
     * @return bool
     */
    public static function unregister(string $key): bool {
        $key = strtolower($key);
        if(isset(self::$conditions[$key])){
            unset(self::$conditions[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * unsuccess
     *
     * @param  string $key
     * @param  ?callable $unsuccess
     * @param  ?array $settings
     * @return $this
     */
    public function unsuccess(string $key, $unsuccess = null, array $settings = []): Validation {
        $key = strtolower($key);
        if(isset(self::$conditions[$key])){
            if($unsuccess === null){
                $unsuccess = self::$conditions[$key][1];
                $settings = self::$conditions[$key][2];
                if(is_string($unsuccess)){
                    $this->api->text($unsuccess)
                    ->markup($settings['markup'] ?? null)
                    ->parse($settings['parse'] ?? null)
                    ->notification($settings['notification'] ?? null)
                    ->protect($settings['protect'] ?? null);
                }elseif($unsuccess){
                    $unsuccess($this->api);
                }
            }else{
                self::$conditions[$key][1] = $unsuccess;
            }
        }
        return $this;
    }
    
    /**
     * validate
     *
     * @param  string $validators
     * @param  ?bool $exitOnFalse
     * @param  ?bool $justCheck
     * @return bool
     */
    public function validate(string $validators, bool $exitOnFalse = false, bool $justCheck = false): bool {
        $probables = explode('||', $validators);
        do {
            $validators = array_shift($probables);
            $probable = isset($probables[0]);
            if(is_string($validators)){
                $validators = explode('&', $validators);
            }
            foreach($validators as $key => $validator){
                if(is_numeric($key)){
                    $validator = explode(':', $validator, 2);
                    if(isset($validator[1])){
                        $params = explode(',', $validator[1]);
                    }else{
                        $params = [];
                    }
                    $validator = strtolower($validator[0]);
                }else{
                    $params = $validator;
                    $validator = $key;
                    if(!$params){
                        $params = [];
                    }elseif(is_string($params)){
                        $params = explode(',', $params) ?? [];
                    }
                }
                if(isset(self::$conditions[$validator])){
                    [$validator, $unsuccess, $settings] = self::$conditions[$validator];
                    $validate = is_callable($validator) ? $validator($this, ...$params) : $this->validate($validator, false, true);
                    if(!$validate){
                        if($probable){
                            continue 2;
                        }
                        if($justCheck){
                            return false;
                        }
                        if(is_string($unsuccess)){
                            foreach($params as $index => $param)
                                $unsuccess = str_replace(':'.$index, $param, $unsuccess);
                            $this->api->text($unsuccess)
                            ->markup($settings['markup'] ?? null)
                            ->parse($settings['parse'] ?? null)
                            ->notification($settings['notification'] ?? null)
                            ->protect($settings['protect'] ?? null);
                        }elseif($unsuccess){
                            $unsuccess($this->api, ...$params);
                        }
                        if($exitOnFalse){
                            die;
                        }
                        return false;
                    }
                }
            }
        }while($probable);
        return true;
    }
}

Validation::registerAll([
    "text" => fn(Validation $validator, string $text = null) => $validator->text($text),
    "command" => fn(Validation $validator, string $prefix = '/', string $command = null) => $validator->command($prefix, $command),
    "startWith" => fn(Validation $validator, string $needle) => $validator->startWith($needle),
    "startiWith" => fn(Validation $validator, string $needle) => $validator->startiWith($needle),
    "endWith" => fn(Validation $validator, string $needle) => $validator->endWith($needle),
    "endiWith" => fn(Validation $validator, string $needle) => $validator->endiWith($needle),
    "callbackStartWith" => fn(Validation $validator, string $needle) => $validator->callbackStartWith($needle),
    "callbackStartiWith" => fn(Validation $validator, string $needle) => $validator->callbackStartiWith($needle),
    "callbackEndWith" => fn(Validation $validator, string $needle) => $validator->callbackEndWith($needle),
    "callbackEndiWith" => fn(Validation $validator, string $needle) => $validator->callbackEndiWith($needle),
    "inlineStartWith" => fn(Validation $validator, string $needle) => $validator->inlineStartWith($needle),
    "inlineStartiWith" => fn(Validation $validator, string $needle) => $validator->inlineStartiWith($needle),
    "inlineEndWith" => fn(Validation $validator, string $needle) => $validator->inlineEndWith($needle),
    "inlineEndiWith" => fn(Validation $validator, string $needle) => $validator->inlineEndiWith($needle),
    "start" => fn(Validation $validator) => $validator->start(),
    "pregText" => fn(Validation $validator, ...$pattern) => $validator->pregText(implode(',', $pattern)),
    "photo" => fn(Validation $validator) => $validator->photo(),
    "document" => fn(Validation $validator) => $validator->document(),
    "video" => fn(Validation $validator) => $validator->video(),
    "dice" => fn(Validation $validator, string $emoji = null, int $value = null) => $validator->dice($emoji, $value),
    "audio" => fn(Validation $validator) => $validator->audio(),
    "voice" => fn(Validation $validator) => $validator->voice(),
    "videoNote" => fn(Validation $validator) => $validator->videoNote(),
    "sticker" => fn(Validation $validator) => $validator->sticker(),
    "location" => fn(Validation $validator) => $validator->location(),
    "venue" => fn(Validation $validator) => $validator->venue(),
    "animation" => fn(Validation $validator) => $validator->animation(),
    "poll" => fn(Validation $validator) => $validator->poll(),
    "same" => fn(Validation $validator, string $text = null) => $validator->same($text),
    "texti" => fn(Validation $validator, string $text = null) => $validator->texti($text),
    "numeric" => fn(Validation $validator) => $validator->numeric(),
    "callback" => fn(Validation $validator, string $callback) => $validator->callback($callback),
    "pregCallback" => fn(Validation $validator, ...$pattern) => $validator->pregCallback(implode(',', $pattern)),
    "query" => fn(Validation $validator, string $query) => $validator->inline($query),
    "pregQuery" => fn(Validation $validator, ...$pattern) => $validator->pregInline(implode(',', $pattern)),
    "group" => fn(Validation $validator) => $validator->group(),
    "replyed" => fn(Validation $validator) => $validator->replyed(),
    "joined" => fn(Validation $validator) => $validator->joined(),
    "admin" => fn(Validation $validator) => $validator->admin(),
    "min" => fn(Validation $validator, int $length) => $validator->minText($length),
    "max" => fn(Validation $validator, int $length) => $validator->maxText($length),
    "minNum" => fn(Validation $validator, int $num) => $validator->text() && $validator->message->text >= $num,
    "maxNum" => fn(Validation $validator, int $num) => $validator->text() && $validator->message->text <= $num,
    "integer" => fn(Validation $validator) => $validator->numeric() && floor($validator->message->text) == $validator->message->text,
    "filterText" => fn(Validation $validator, $constant) => $validator->filterText(is_numeric($constant) ? $constant : constant($constant)),
    "positive" => fn(Validation $validator) => $validator->text() && $validator->message->text >= 0,
    "negative" => fn(Validation $validator) => $validator->text() && $validator->message->text <= 0,
    "nonZero" => fn(Validation $validator) => $validator->text() && $validator->message->text != 0,
]);