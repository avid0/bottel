<?php
namespace Bottel\Traits;

trait Downloadable {    
    /**
     * contents
     *
     * @return void
     */
    public function contents(){
    }
        
    /**
     * download
     *
     * @param  mixed $into
     * @return void
     */
    public function download(string $into){
    }

    public function getDownloadableId(): string {
        return $this->file_id ?: null;
    }
}
