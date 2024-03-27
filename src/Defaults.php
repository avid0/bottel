<?php
namespace Bottel;

class Defaults {    
    /**
     * @var bool $resize_keyboard
     */
    public $resize_keyboard = false;
        
    /**
     * @var ?string $parse_mode
     */
    public $parse_mode = null;

    /**
     * @var bool $auto_action
     */
    public $auto_action = false;
    
    /**
     * @var bool $web_preview
     */
    public $web_preview = null;

    /**
     * @var bool $show_alert
     */
    public $show_alert = null;

    /**
     * @var bool $auto_answer_callback
     */
    public $auto_answer_callback = false;

    /**
     * @var ?string $callback_data
     */
    public $callback_data = null;
}