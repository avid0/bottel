<?php
namespace Bottel\Traits;

trait KeyboardBag {    
    /**
     * @var array
     */
    public $buttons = array();    

    /**
     * @var array
     */
    public $rows = array();    

    /**
     * @var array
     */
    public $keyboards = array();
    
    /**
     * keyboard
     *
     * @param  string $index
     * @param  mixed $rows
     * @return $this
     */
    public function keyboard(string $index, $rows){
        $this->keyboards[$index] = $rows;
        return $this;
    }
    
    /**
     * row
     *
     * @param  string $index
     * @param  mixed $row
     * @return $this
     */
    public function row(string $index, $row){
        $this->rows[$index] = $row;
        return $this;
    }
    
    /**
     * addRow
     *
     * @param  string $index
     * @param  mixed $row
     * @return $this
     */
    public function addRow(string $index, $row){
        if(!$this->hasKeyboard($index)){
            return $this;
        }
        if(isset($this->keyboards[$index]['keyboard']))
            $this->keyboards[$index]['keyboard'][] = $row;
        elseif(isset($this->keyboards[$index]['inline_keyboard']))
            $this->keyboards[$index]['inline_keyboard'][] = $row;
        return $this;
    }
    
    /**
     * addButton
     *
     * @param  string $index
     * @param  array $button
     * @return $this
     */
    public function addButton(string $index, array $button){
        if(!$this->hasRow($index)){
            return $this;
        }
        $this->rows[$index][] = $button;
        if(count($this->rows[$index]) > 8){
            $this->rows[$index] = array_slice($this->rows[$index], 0, 8);
        }
        return $this;
    }
    
    /**
     * textButton
     *
     * @param  string $index
     * @param  string $name
     * @return $this
     */
    public function textButton(string $index, string $name){
        $this->buttons[$index] = array(
            "text" => $name
        );
        return $this;
    }

    /**
     * callableButton
     *
     * @param  string $index
     * @param  string $name
     * @param  mixed $callback
     * @param  string $callback_data
     * @return $this
     */
    public function callableButton(string $index, string $name, $callback, string $callback_data){
        $this->buttons[$index] = array(
            "text" => $name,
            "callback" => $callback,
        );
        if($callback_data){
            $this->buttons[$index]['callback_data'] = $callback_data;
        }
        return $this;
    }
    
    /**
     * urlButton
     *
     * @param  string $index
     * @param  string $name
     * @param  string $url
     * @return $this
     */
    public function urlButton(string $index, string $name, string $url){
        $this->buttons[$index] = array(
            "text" => $name,
            "url" => $url
        );
        return $this;
    }
    
    /**
     * callbackButton
     *
     * @param  string $index
     * @param  string $name
     * @param  string $data
     * @return $this
     */
    public function callbackButton(string $index, string $name, string $data){
        $this->buttons[$index] = array(
            "text" => $name,
            "callback_data" => $data
        );
        return $this;
    }
    
    /**
     * queryButton
     *
     * @param  string $index
     * @param  string $name
     * @param  string $query
     * @return $this
     */
    public function queryButton(string $index, string $name, string $query){
        $this->buttons[$index] = array(
            "text" => $name,
            "switch_inline_query" => $query
        );
        return $this;
    }
    
    /**
     * hasKeyboard
     *
     * @param  bool $index
     * @return bool
     */
    public function hasKeyboard(string $index): bool {
        return isset($this->keyboards[$index]);
    }
    
    /**
     * hasRow
     *
     * @param  string $index
     * @return bool
     */
    public function hasRow(string $index): bool {
        return isset($this->rows[$index]);
    }
    
    /**
     * hasButton
     *
     * @param  mixed $index
     * @return bool
     */
    public function hasButton(string $index): bool {
        return isset($this->buttons[$index]);
    }
        
    /**
     * has
     *
     * @param  mixed $index
     * @return bool
     */
    public function has(string $index): bool {
        return $this->hasKeyboard($index) || $this->hasRow($index) || $this->hasButton($index);
    }
    
    /**
     * getKeyboard
     *
     * @param  string $index
     * @return array|false
     */
    public function getKeyboard(string $index): array|false {
        if($this->hasKeyboard($index)){
            $keyboard = $this->keyboards[$index];
            $keyboard = value($keyboard);
            return $keyboard;
        }
        return false;
    }
    
    /**
     * getRow
     *
     * @param  string $index
     * @return array|false
     */
    public function getRow(string $index): array|false {
        if($this->hasRow($index)){
            $row = $this->rows[$index];
            return value($row);
        }
        return false;
    }
    
    /**
     * getButton
     *
     * @param  string $index
     * @return array|false
     */
    public function getButton(string $index): array|false {
        if($this->hasButton($index)){
            $button = $this->buttons[$index];
            return value($button);
        }
        return false;
    }
    
    /**
     * get
     *
     * @param  string $index
     * @return array|false
     */
    public function get(string $index): array|false {
        if($this->hasKeyboard($index))
            return $this->getKeyboard($index);
        if($this->hasRow($index))
            return $this->getRow($index);
        if($this->hasButton($index))
            return $this->getButton($index);
        if($this->hasMenu($index))
            return $this->getMenu($index);
        return false;
    }
    
    /**
     * remove
     *
     * @param  string $index
     * @return bool
     */
    public function remove(string $index): bool {
        if($this->hasKeyboard($index))
            unset($this->keyboards[$index]);
        elseif($this->hasRow($index))
            unset($this->rows[$index]);
        elseif($this->hasButton($index))
            unset($this->buttons[$index]);
        else
            return false;
        return true;
    }
    
    /**
     * reset
     *
     * @return void
     */
    public function reset(){
        $this->keyboards = $this->rows = $this->buttons = array();
    }
}