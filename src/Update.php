<?php
namespace Bottel;
use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * Update
 *
 * @method int getId()
 * @method string getType()
 * @method Types\Message getRecursiveMessage()
 * @method Types\Chat getChat()
 * @method Types\User getUser()
 * @method int getDate()
 * @method int getUpdateId()
 * @method Types\Message getMessage()
 * @method Types\Message getEditedMessage()
 * @method Types\Message getChannelPost()
 * @method Types\Message getEditedChannelPost()
 * @method Types\InlineQuery getInlineQuery()
 * @method Types\ChosenInlineResult getChosenInlineResult()
 * @method Types\CallbackQuery getCallbackQuery()
 * @method Types\ShippingQuery getShippingQuery()
 * @method Types\PreCheckoutQuery getPreCheckoutQuery()
 * @method Types\Poll getPoll()
 * @method Types\PollAnswer getPollAnswer()
 * @method Types\ChatMemberUpdated getMyChatMember()
 * @method Types\ChatMemberUpdated getChatMember()
 * @method Types\ChatJoinRequest getChatJoinRequest()
 *
 * @method bool hasId()
 * @method bool hasType()
 * @method bool hasRecursiveMessage()
 * @method bool hasChat()
 * @method bool hasUser()
 * @method bool hasDate()
 * @method bool isUpdateId()
 * @method bool isMessage()
 * @method bool isEditedMessage()
 * @method bool isChannelPost()
 * @method bool isEditedChannelPost()
 * @method bool isInlineQuery()
 * @method bool isChosenInlineResult()
 * @method bool isCallbackQuery()
 * @method bool isShippingQuery()
 * @method bool isPreCheckoutQuery()
 * @method bool isPoll()
 * @method bool isPollAnswer()
 * @method bool isMyChatMember()
 * @method bool isChatMember()
 * @method bool isChatJoinRequest()
 *
 * @method $this setId(int $value)
 * @method $this setType(string $value)
 * @method $this setRecursiveMessage(Types\Message $value)
 * @method $this setChat(Types\Chat $value)
 * @method $this setUser(Types\User $value)
 * @method $this setDate(int $value)
 * @method $this setUpdateId(int $value)
 * @method $this setMessage(Types\Message $value)
 * @method $this setEditedMessage(Types\Message $value)
 * @method $this setChannelPost(Types\Message $value)
 * @method $this setEditedChannelPost(Types\Message $value)
 * @method $this setInlineQuery(Types\InlineQuery $value)
 * @method $this setChosenInlineResult(Types\ChosenInlineResult $value)
 * @method $this setCallbackQuery(Types\CallbackQuery $value)
 * @method $this setShippingQuery(Types\ShippingQuery $value)
 * @method $this setPreCheckoutQuery(Types\PreCheckoutQuery $value)
 * @method $this setPoll(Types\Poll $value)
 * @method $this setPollAnswer(Types\PollAnswer $value)
 * @method $this setMyChatMember(Types\ChatMemberUpdated $value)
 * @method $this setChatMember(Types\ChatMemberUpdated $value)
 * @method $this setChatJoinRequest(Types\ChatJoinRequest $value)
 *
 * @method $this unsetId()
 * @method $this unsetType()
 * @method $this unsetRecursiveMessage()
 * @method $this unsetChat()
 * @method $this unsetUser()
 * @method $this unsetDate()
 * @method $this unsetUpdateId()
 * @method $this unsetMessage()
 * @method $this unsetEditedMessage()
 * @method $this unsetChannelPost()
 * @method $this unsetEditedChannelPost()
 * @method $this unsetInlineQuery()
 * @method $this unsetChosenInlineResult()
 * @method $this unsetCallbackQuery()
 * @method $this unsetShippingQuery()
 * @method $this unsetPreCheckoutQuery()
 * @method $this unsetPoll()
 * @method $this unsetPollAnswer()
 * @method $this unsetMyChatMember()
 * @method $this unsetChatMember()
 * @method $this unsetChatJoinRequest()
 *
 * @property int $update_id
 * @property int $id
 * @property string $type
 * @property Types\Message $recursive_message
 * @property Types\Chat $chat
 * @property Types\User $user
 * @property int $date
 * @property Types\Message $message
 * @property Types\Message $edited_message
 * @property Types\Message $channel_post
 * @property Types\Message $edited_channel_post
 * @property Types\InlineQuery $inline_query
 * @property Types\ChosenInlineResult $chosen_inline_result
 * @property Types\CallbackQuery $callback_query
 * @property Types\ShippingQuery $shipping_query
 * @property Types\PreCheckoutQuery $pre_checkout_query
 * @property Types\Poll $poll
 * @property Types\PollAnswer $poll_answer
 * @property Types\ChatMemberUpdated $my_chat_member
 * @property Types\ChatMemberUpdated $chat_member
 * @property Types\ChatJoinRequest $chat_join_request
 */
class Update extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'id' => 'int',
        'update_id' => 'int',
        'type' => 'string',
        'inline_message_id' => 'string',
        'message' => 'Types\Message',
        'edited_message' => 'Types\Message',
        'channel_post' => 'Types\Message',
        'edited_channel_post' => 'Types\Message',
        'inline_query' => 'Types\InlineQuery',
        'chosen_inline_result' => 'Types\ChosenInlineResult',
        'callback_query' => 'Types\CallbackQuery',
        'shipping_query' => 'Types\ShippingQuery',
        'pre_checkout_query' => 'Types\PreCheckoutQuery',
        'poll' => 'Types\Poll',
        'poll_answer' => 'Types\PollAnswer',
        'my_chat_member' => 'Types\ChatMemberUpdated',
        'chat_member' => 'Types\ChatMemberUpdated',
        'chat_join_request' => 'Types\ChatJoinRequest',
        'recursive_message' => 'Types\Message',
        'user' => 'Types\User',
        'chat' => 'Types\Chat',
        'date' => 'int',
    ];

    private static $update_types = [
        'message',
        'edited_message',
        'channel_port',
        'edited_channel_port',
        'iniline_query',
        'chosen_inline_result',
        'callback_query',
        'shipping_query',
        'pre_checkout_query',
        'poll',
        'poll_answer',
        'my_chat_member',
        'chat_member',
        'chat_join_request',
    ];

    protected function _init(){
        parent::_init();

        $this->id = $this->update_id;
        foreach(self::$update_types as $type){
            if(isset($this->$type)){
                $this->type = $type;
                break;
            }
        }

        switch($type){
            case 'message':
            case 'edited_message':
            case 'channel_post':
            case 'edited_channel_post':
                $message = $this->$type;
                $this->recursive_message = $message;
                break;
            case 'callback_query':
                $this->recursive_message = $this->callback_query->message;
            break;
            case 'inline_query':
            case 'shipping_query':
            case 'pre_checkout_query':
                $this->user = $this->$type->from;
            break;
            case 'chosen_inline_result':
                $this->user = $this->chosen_inline_result->from;
                $this->inline_message_id = $this->chosen_inline_result->inline_message_id;
            break;
            case 'poll':
            break;
            case 'poll_answer':
                $this->chat = $this->poll_answer->voter_chat;
                $this->user = $this->poll_answer->user;
            break;
            case 'my_chat_member':
            case 'chat_member':
            case 'chat_join_request':
                $this->chat = $this->$type->chat;
                $this->user = $this->$type->from;
            break;
        }

        $this->user ??= $this->$type->from ?? $this->$type->message->from ?? $this->$type->user;
        $this->chat ??= $this->$type->chat ?? $this->$type->message->chat;
        if(!$this->chat && $this->user){
            $this->chat = new Types\Chat([
                'id' => $this->user->id,
                'type' => 'private',
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'username' => $this->user->username,
            ]);
        }

        $this->date = $this->$type->date ?? $this->recursive_message->date;
    }
}

?>
