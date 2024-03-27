<?php
namespace Bottel\Request;

use Bottel\Defaults;
use Bottel\Symbolic;
use Bottel\Language;
use Bottel\Types\CallbackQuery;
use Bottel\Types\RawRequest;

class Interpreter {
    private static $json_fields = [
        'reply_markup',
        'results',
        'button',
        'media',
    ];
    
    /**
     * dejson
     *
     * @param  array $datas
     * @return array
     */
    public static function dejson(array $datas): array {
        foreach(self::$json_fields as $field){
            if(isset($datas[$field]) && is_string($datas[$field])){
                $decode = json_decode($datas[$field], true);
                if($decode){
                    $datas[$field] = $decode;
                }
            }
        }
        return $datas;
    }
    
    /**
     * jsonify
     *
     * @param  array $datas
     * @return array
     */
    public static function jsonify(array $datas): array {
        foreach(self::$json_fields as $field){
            if(isset($datas[$field])){
                $datas[$field] = json_encode($datas[$field]);
            }
        }
        return $datas;
    }
    
    /**
     * prepareReplyMarkup
     *
     * @param  array $datas
     * @return array
     */
    public static function prepareReplyMarkup(array $datas): array {
        if(isset($datas['keyboard'])){
            $datas['reply_markup'] = [
                'keyboard' => $datas['keyboard'],
            ];
            unset($datas['keyboard']);
        }elseif(isset($datas['inline_keyboard'])){
            $datas['reply_markup'] = [
                'inline_keyboard' => $datas['inline_keyboard'],
            ];
            unset($datas['inline_keyboard']);
        }
        if(isset($datas['results']) && is_array($datas['results'])){
            foreach($datas['results'] as $result){
                $result = self::prepareReplyMarkup($result);
            }
        }
        return $datas;
    }
    
    /**
     * normalizeParseMode
     *
     * @param  array $datas
     * @return array
     */
    public static function normalizeParseMode(array $datas): array {
        if(isset($datas['parse_mode'])){
            switch(strtolower($datas['parse_mode'])){
                case 'markdownv2':
                    $datas['parse_mode'] = 'MarkDownV2';
                break;
                case 'markdown':
                    $datas['parse_mode'] = 'MarkDown';
                break;
                case 'html':
                    $datas['parse_mode'] = 'HTML';
                break;
                case 'raw':
                    unset($datas['parse_mode']);
                break;
            }
        }
        return $datas;
    }
    
    /**
     * serializeText
     *
     * @param  array $datas
     * @return array
     */
    public static function serializeText(array $datas): array {
        if(isset($datas['text']) && !is_string($datas['text']) && !is_numeric($datas['text'])){
            $datas['text'] = json_encode($datas['text'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $datas['text'] = '`'.str_replace(['\\', '`'], ['\\\\', '\\`'], $datas['text']).'`';
            $datas['parse_mode'] = 'MarkDownV2';
        }elseif(isset($datas['caption']) && !is_string($datas['caption']) && !is_numeric($datas['caption'])){
            $datas['caption'] = json_encode($datas['caption'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $datas['caption'] = '`'.str_replace(['\\', '`'], ['\\\\', '\\`'], $datas['caption']).'`';
            $datas['parse_mode'] = 'MarkDownV2';
        }
        return $datas;
    }

    /**
     * applyDefaults
     *
     * @param  string $method
     * @param  array $datas
     * @param  Defaults $defaults
     * @return array
     */
    public static function applyDefaults(string $method, array $datas, Defaults $defaults): array {
        if(!isset($datas['parse_mode']) && $defaults->parse_mode)
            $datas['parse_mode'] = $defaults->parse_mode;
        if(isset($datas['reply_markup'])){
            if(is_string($datas['reply_markup'])){
                $reply_markup = json_decode($datas['reply_markup'], true);
                if($reply_markup){
                    $datas['reply_markup'] = $reply_markup;
                }
            }
            if((!isset($datas['reply_markup']['resize_keyboard']) || $datas['reply_markup']['resize_keyboard'] === null)
               && isset($datas['reply_markup']['keyboard']) && $defaults->resize_keyboard){
                $datas['reply_markup']['resize_keyboard'] = $defaults->resize_keyboard;
            }
        }
        switch(strtolower($method)){
            case 'answercallbackquery':
                if(!isset($datas['show_alert']) && $defaults->show_alert !== null){
                    $datas['show_alert'] = $defaults->show_alert;
                }
            break;
            case 'sendmessage':
                if(!isset($datas['disable_web_page_preview']) && $defaults->web_preview !== null){
                    $datas['disable_web_page_preview'] = $defaults->web_preview;
                }
            break;
        }
        return $datas;
    }
    
    /**
     * prepareAutoAction
     *
     * @param  string $method
     * @param  array $datas
     * @return ?RawRequest
     */
    public static function prepareAutoAction(string $method, array $datas): ?RawRequest {
        if(isset($datas['chat_id'])){
            switch(strtolower($method)){
                case 'sendmessage':
                    $action = 'typing';
                break;
                case 'sendphoto':
                    $action = 'upload_photo';
                break;
                case 'sendvideo':
                    $action = 'upload_video';
                break;
                case 'sendaudio':
                    $action = 'upload_audio';
                break;
                case 'senddocument':
                    $action = 'upload_document';
                break;
                case 'sendlocation':
                    $action = 'find_location';
                break;
                case 'sendvideonote':
                    $action = 'upload_video_note';
                break;
                default:
                    $action = null;
            }
            if($action){
                return new RawRequest('sendChatAction', [
                    'chat_id' => $datas['chat_id'],
                    'action' => $action,
                ]);
            }
        }
        return null;
    }
    
    /**
     * flattenObjectFields
     *
     * @param  array $datas
     * @return array
     */
    public static function flattenObjectFields(array $datas): array {
        if(isset($datas['chat_id']) && is_object($datas['chat_id'])){
            if(isset($datas['chat_id']->message))
                $datas['chat_id'] = $datas['chat_id']->message;
            if(isset($datas['chat_id']->chat))
                $datas['chat_id'] = $datas['chat_id']->chat;
            if(isset($datas['chat_id']->from))
                $datas['chat_id'] = $datas['chat_id']->from;
            if(isset($datas['chat_id']->user))
                $datas['chat_id'] = $datas['chat_id']->user;
            if(isset($datas['chat_id']->id))
                $datas['chat_id'] = $datas['chat_id']->id;
        }
        if(isset($datas['user_id']) && is_object($datas['user_id'])){
            if(isset($datas['user_id']->message))
                $datas['user_id'] = $datas['user_id']->message;
            if(isset($datas['user_id']->user))
                $datas['user_id'] = $datas['user_id']->user;
            if(isset($datas['user_id']->from))
                $datas['user_id'] = $datas['user_id']->from;
            if(isset($datas['user_id']->chat))
                $datas['user_id'] = $datas['user_id']->chat;
            if(isset($datas['user_id']->id))
                $datas['user_id'] = $datas['user_id']->id;
        }
        if(isset($datas['message_id']) && is_object($datas['message_id'])){
            if(isset($datas['message_id']->message))
                $datas['message_id'] = $datas['message_id']->message;
            if(isset($datas['message_id']->message_id))
                $datas['message_id'] = $datas['message_id']->message_id;
        }
        if(isset($datas['reply_to_message_id']) && is_object($datas['reply_to_message_id'])){
            if(isset($datas['reply_to_message_id']->message))
                $datas['reply_to_message_id'] = $datas['reply_to_message_id']->message;
            if(isset($datas['reply_to_message_id']->message_id))
                $datas['reply_to_message_id'] = $datas['reply_to_message_id']->message_id;
        }
        return $datas;
    }
    
    /**
     * handleFile
     *
     * @param  array $datas
     * @param  string $method
     * @param  Symbolic $symbolic
     * @return array
     */
    public static function handleFile(array $datas, string $method, Symbolic $symbolic = null): array {
        $index = $file = null;
        $method = strtolower($method);
        if(in_array($method, [
            'sendphoto',
            'sendaudio',
            'senddocument',
            'sendvideo',
            'sendanimation',
            'sendvoice',
            'sendsticker',
        ])){
            $index = substr($method, 4);
        }elseif($method == 'sendvideonote'){
            $index = 'video_note';
        }
        if($index && isset($datas[$index]))
            $file = $datas[$index];
        elseif(isset($datas['file'])){
            $file = $datas['file'];
            unset($datas['file']);
        }
        // flatten file object
        if($file && is_object($file)){
            if(isset($file->message))
                $file = $file->message;
            if(isset($file->photo) && is_array($file->photo)){
                $file = $file->photo;
                $file = $file[count($file)-1];
            }elseif(isset($file->$index))
                $file = $file->$index;
            if(isset($file->file_id))
                $file = $file->file_id;
        }
        if($file && is_string($file)){
            if(isset($datas['mime_type'])){
                $mimetype = $datas['mime_type'];
                unset($datas['mime_type']); 
            }else
                $mimetype = '';
            if(isset($datas['file_name'])){
                $filename = $datas['file_name'];
                unset($datas['file_name']);
            }else
                $filename = '';
            if(strpos($file, '@') === 0)
                $datas[$index] = new \CURLFile(substr($file, 1), $mimetype, $filename);
            else
                $datas[$index] = $file;
        }elseif($file && is_object($file)){
            if(isset($file->$index))
                $file = $file->$index;
            if($index == 'photo' && is_array($file)){
                $lfk = count($file) - 1;
                if(isset($file[$lfk]->file_id))
                    $file = $file[$lfk]->file_id;
            }
            if(isset($file->file_id))
                $file = $file->file_id;
        }elseif($file && is_array($file) && $index == 'photo'){
            $lfk = count($file) - 1;
            if(isset($file[$lfk]->file_id))
                $file = $file[$lfk]->file_id;
        }
        if(isset($datas['thumb']) && is_string($datas['thumb'])){
            $thumb = $datas['thumb'];
            if(strpos($thumb, '@') === 0)
                $thumb = new \CURLFile(substr($thumb, 1));
            $datas['thumb'] = $thumb;
        }
        if(isset($datas['results'])){
            foreach($datas['results'] as &$result){
                foreach([
                    "audio",
                    "document",
                    "gif",
                    "mpeg4",
                    "photo",
                    "sticker",
                    "video",
                    "voice",
                    "thumbnail",
                ] as $type){
                    if(isset($result[$type])){
                        if(strpos($result[$type], '@') === 0 && $symbolic)
                            $result["{$type}_url"] = $symbolic->file(substr($result[$type], 1));
                        elseif(strpos($result[$type], '/') !== false || strpos($result[$type], '.') !== false)
                            $result["{$type}_url"] = $result[$type];
                        else
                            $result["{$type}_file_id"] = $result[$type];
                        unset($result[$type]);
                    }
                }
            }
        }
        if(isset($datas['media']['media'])){
            $file = $datas['media']['media'];
            if(isset($datas['media']['media']['mime_type'])){
                $mimetype = $datas['media']['media']['mime_type'];
                unset($datas['media']['media']['mime_type']); 
            }else
                $mimetype = '';
            if(isset($datas['media']['media']['file_name'])){
                $filename = $datas['media']['media']['file_name'];
                unset($datas['media']['media']['file_name']);
            }else
                $filename = '';
            if(strpos($file, '@') === 0)
                $file = new \CURLFile(substr($file, 1), $mimetype, $filename);
            $datas['media']['media'] = $file;
        }
        return $datas;
    }
    
    /**
     * removeRedundantChatId
     *
     * @param  array $datas
     * @return array
     */
    public static function removeRedundantChatId(array $datas): array {
        if(isset($datas['inline_message_id']) && isset($datas['chat_id']) && !isset($datas['message_id'])){
            unset($datas['chat_id']);
        }
        return $datas;
    }
    
    /**
     * callbackQueryAnswered
     *
     * @param  array $datas
     * @param  string $method
     * @param  CallbackQuery $current
     * @return void
     */
    public static function callbackQueryAnswered(array $datas, string $method, CallbackQuery $current){
        $method = strtolower($method);
        if($method == 'answercallbackquery' && isset($datas['callback_query_id']) && $datas['callback_query_id'] == $current){
            return true;
        }
        if(str_starts_with($method, 'edit') && isset($datas['message_id']) && isset($current->message) && $current->message->message_id == $datas['message_id']){
            return true;
        }
        return false;
    }
}