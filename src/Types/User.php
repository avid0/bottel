<?php
namespace Bottel\Types;

use Bottel\Traits\ChatManager;
use Bottel\Traits\Quoter;
use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * User
 *
 * @method Int getIsAdmin()
 * @method Int getIsSuperAdmin()
 * @method Int getId()
 * @method bool getIsBot()
 * @method bool getIsSelf()
 * @method string getFirstName()
 * @method string getFullName()
 * @method string getLastName()
 * @method string getUsername()
 * @method string getLanguageCode()
 * @method string getMention()
 * @method bool getCanJoinGroups()
 * @method bool getCanReadAllGroupMessages()
 * @method bool getSupportsInlineQueries()
 *
 * @method bool isIsAdmin()
 * @method bool isIsSuperAdmin()
 * @method bool isId()
 * @method bool isIsBot()
 * @method bool isIsSelf()
 * @method bool isFirstName()
 * @method bool isFullName()
 * @method bool isLastName()
 * @method bool isUsername()
 * @method bool isLanguageCode()
 * @method bool isMention()
 * @method bool isCanJoinGroups()
 * @method bool isCanReadAllGroupMessages()
 * @method bool isSupportsInlineQueries()
 *
 * @method $this setIsAdmin(int $value)
 * @method $this setIsSuperAdmin(int $value)
 * @method $this setId(int $value)
 * @method $this setIsBot(bool $value)
 * @method $this setIsSelf(bool $value)
 * @method $this setFirstName(string $value)
 * @method $this setFullName(string $value)
 * @method $this setLastName(string $value)
 * @method $this setUsername(string $value)
 * @method $this setLanguageCode(string $value)
 * @method $this setMention(string $value)
 * @method $this setCanJoinGroups(bool $value)
 * @method $this setCanReadAllGroupMessages(bool $value)
 * @method $this setSupportsInlineQueries(bool $value)
 *
 * @method $this unsetIsAdmin()
 * @method $this unsetIsSuperAdmin()
 * @method $this unsetId()
 * @method $this unsetIsBot()
 * @method $this unsetIsSelf()
 * @method $this unsetFirstName()
 * @method $this unsetFullName()
 * @method $this unsetLastName()
 * @method $this unsetUsername()
 * @method $this unsetLanguageCode()
 * @method $this unsetMention()
 * @method $this unsetCanJoinGroups()
 * @method $this unsetCanReadAllGroupMessages()
 * @method $this unsetSupportsInlineQueries()
 *
 * @property bool $is_admin
 * @property bool $is_super_admin
 * @property Int $id
 * @property bool $is_bot
 * @property bool $is_self
 * @property string $first_name
 * @property string $full_name
 * @property string $last_name
 * @property string $username
 * @property string $language_code
 * @property string $mention
 * @property bool $can_join_groups
 * @property bool $can_read_all_group_messages
 * @property bool $supports_inline_queries
 */
class User extends DelayedJsonMapper {
    use ChatManager, Quoter;

    const JSON_PROPERTY_MAP = [
        Chat::class,
        'id' => 'int',
        'is_bot' => 'bool',
        'first_name' => 'string',
        'last_name' => 'string',
        'username' => 'string',
        'language_code' => 'string',
        'can_join_groups' => 'bool',
        'can_read_all_group_messages' => 'bool',
        'supports_inline_queries' => 'bool',
    ];
}