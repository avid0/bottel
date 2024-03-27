<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class BotCommandScope extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        BotCommandScopeDefault::class,
        BotCommandScopeAllPrivateChats::class,
        BotCommandScopeAllGroupChats::class,
        BotCommandScopeAllChatAdministrators::class,
        BotCommandScopeChat::class,
        BotCommandScopeChatAdministrators::class,
        BotCommandScopeChatMember::class,
    ];
}