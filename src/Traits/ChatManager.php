<?php
namespace Bottel\Traits;

use Bottel\Types\Chat;
use Bottel\Types\User;

trait ChatManager {
    public function text(string $text, ...$args){
    }

    public function getReceptor(): ?int {
        if(isset($this->chat) && $this->chat instanceof Chat){
            return $this->chat->id;
        }
        if(isset($this->user) && $this->user instanceof User){
            return $this->user->id;
        }
        if(isset($this->from) && $this->from instanceof User){
            return $this->from->id;
        }
        if($this instanceof User || $this instanceof Chat){
            return $this->id;
        }
    }
}

?>