<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * ChatMemberOwner
 *
 * @method string getStatus()
 * @method User getUser()
 * @method bool getIsAnonymous()
 * @method string getCustomTitle()
 *
 * @method bool isStatus()
 * @method bool isUser()
 * @method bool isIsAnonymous()
 * @method bool isCustomTitle()
 *
 * @method $this setStatus(string $value)
 * @method $this setUser(User $value)
 * @method $this setIsAnonymous(bool $value)
 * @method $this setCustomTitle(string $value)
 *
 * @method $this unsetStatus()
 * @method $this unsetUser()
 * @method $this unsetIsAnonymous()
 * @method $this unsetCustomTitle()
 *
 * @property string $status
 * @property User $user
 * @property bool $is_anonymous
 * @property string $custom_title
 */
class ChatMemberOwner extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'status' => 'string',
        'user' => 'User',
        'is_anonymous' => 'bool',
        'custom_title' => 'string',
        'is_member' => 'bool',
    ];
    
    public function _init(){
        parent::_init();
        $this->_setProperty('is_member', true);
    }
}