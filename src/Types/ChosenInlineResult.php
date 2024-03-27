<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;
use Bottel\Traits\MessageManager;

/**
 * ChosenInlineResult
 *
 * @method string getResultId()
 * @method User getFrom()
 * @method Location getLocation()
 * @method string getInlineMessageId()
 * @method string getQuery()
 *
 * @method bool isResultId()
 * @method bool isFrom()
 * @method bool isLocation()
 * @method bool isInlineMessageId()
 * @method bool isQuery()
 *
 * @method $this setResultId(string $value)
 * @method $this setFrom(User $value)
 * @method $this setLocation(Location $value)
 * @method $this setInlineMessageId(string $value)
 * @method $this setQuery(string $value)
 *
 * @method $this unsetResultId()
 * @method $this unsetFrom()
 * @method $this unsetLocation()
 * @method $this unsetInlineMessageId()
 * @method $this unsetQuery()
 *
 * @property string $result_id
 * @property User $from
 * @property Location $location
 * @property string $inline_message_id
 * @property string $query
 */
class ChosenInlineResult extends DelayedJsonMapper {
    use MessageManager;

    const JSON_PROPERTY_MAP = [
        'result_id' => 'string',
        'from' => 'User',
        'location' => 'Location',
        'inline_message_id' => 'string',
        'query' => 'string',
    ];
}