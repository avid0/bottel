<?php
namespace Bottel;

use Bottel\Traits\KeyboardBag;

class Keyboard {
    use KeyboardBag;
    
    /**
     * rows
     *
     * @param  array $reply_markup
     * @return void
     */
    public function rows(array $reply_markup){
        if(isset($reply_markup['keyboard']))
            return $reply_markup['keyboard'];
        if(isset($reply_markup['inline_keyboard']))
            return $reply_markup['inline_keyboard'];
        return $reply_markup;
    }
    
    /**
     * parseButton
     *
     * @param  array $button
     * @return array
     */
    private function parseButton(array $button): array {
        if(isset($button['web_app']) && is_string($button['web_app'])){
            $button['web_app'] = [
                'url' => $button['web_app'],
            ];
        }
        if(isset($button['inline_query'])){
            $button['switch_inline_query'] = $button['inline_query'];
            unset($button['inline_query']);
        }
        if(isset($button['inline_query_current_chat'])){
            $button['switch_inline_query_current_chat'] = $button['inline_query_current_chat'];
            unset($button['inline_query_current_chat']);
        }
        if(isset($button['inline_query_chosen_chat'])){
            $button['switch_inline_query_chosen_chat'] = $button['inline_query_chosen_chat'];
            unset($button['inline_query_chosen_chat']);
        }
        if(isset($button['start'])){
            $button['url'] = "https://t.me/".config("BOT_USERNAME")."?start=".$button['start'];
            unset($button['start']);
        }
        return $button;
    }

    public function parse(array $reply_markup){
        return $reply_markup;
    }
}