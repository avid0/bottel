<?php
namespace Bottel\Traits;

trait MessageManager {
    private static array $media_types = [
        'animation',
        'audio',
        'document',
        'photo',
        'sticker',
        'video',
        'video_note',
        'voice',
    ];

    private static array $types = [
        'text',
        'animation',
        'audio',
        'document',
        'photo',
        'sticker',
        'video',
        'video_note',
        'voice',
        'contact',
        'dice',
        'game',
        'poll',
        'venue',
        'location',
        'new_chat_members',
        'left_chat_member',
        'new_chat_title',
        'new_chat_photo',
        'delete_chat_photo',
        'group_chat_created',
        'supergroup_chat_created',
        'pinned_message',
        'invoice',
        'successful_payment'
    ];

    public function edit(){}

    public function editCaption(){}

    public function editMarkup(){}

    public function editMedia(){}

    public function delete(){}

    public function forward(){}

    public function copy(){}

    public function repy(){}

    public function pin(){}

    public function unpin(){}

    public function media(): mixed {
        foreach(self::$media_types as $media_type){
            if(isset($this->$media_type)){
                return $this->$media_type;
            }
        }
        return null;
    }
}

?>