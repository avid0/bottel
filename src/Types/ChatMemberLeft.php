<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * ChatMemberLeft
 *
 * @method string getStatus()
 * @method User getUser()
 *
 * @method bool isStatus()
 * @method bool isUser()
 *
 * @method $this setStatus(string $value)
 * @method $this setUser(User $value)
 *
 * @method $this unsetStatus()
 * @method $this unsetUser()
 *
 * @property string $status
 * @property User $user
 */
class ChatMemberLeft extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'status' => 'string',
        'user' => 'User',
        'is_member' => 'bool',
    ];
    
    public function _init(){
        parent::_init();
        $this->_setProperty('is_member', true);
    }
}