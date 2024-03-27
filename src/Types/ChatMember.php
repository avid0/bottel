<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class ChatMember extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        ChatMemberOwner::class,
        ChatMemberAdministrator::class,
        ChatMemberMember::class,
        ChatMemberRestricted::class,
        ChatMemberLeft::class,
        ChatMemberBanned::class,
    ];
}