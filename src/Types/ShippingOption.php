<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;
use Bottel\Traits\Quoter;

/**
 * ShippingOption
 *
 * @method string getId()
 * @method string getTitle()
 * @method LabeledPrice[] getPrices()
 *
 * @method bool isId()
 * @method bool isTitle()
 * @method bool isPrices()
 *
 * @method $this setId(string $value)
 * @method $this setTitle(string $value)
 * @method $this setPrices(LabeledPrice[] $value)
 *
 * @method $this unsetId()
 * @method $this unsetTitle()
 * @method $this unsetPrices()
 *
 * @property string $id
 * @property string $title
 * @property LabeledPrice[] $prices
 */
class ShippingOption extends DelayedJsonMapper {
    use Quoter;

    const JSON_PROPERTY_MAP = [
        'id' => 'string',
        'title' => 'string',
        'prices' => 'LabeledPrice[]',
    ];
}