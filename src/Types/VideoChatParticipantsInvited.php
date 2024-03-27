<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * VideoChatParticipantsInvited
 *
 * @method User[] getUsers()
 *
 * @method bool isUsers()
 *
 * @method $this setUsers(User[] $value)
 *
 * @method $this unsetUsers()
 *
 * @property User[] $users
 */
class VideoChatParticipantsInvited extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'users' => 'User[]',
    ];
}