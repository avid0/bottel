<?php
namespace Bottel\Types;

class RawRequest {    
    /**
     * @var string $method
     */
    public $method;
    
    /**
     * @var array $datas;
     */
    public $datas;
    
    /**
     * __construct
     *
     * @param  string $method
     * @param  ?array $datas
     * @return void
     */
    public function __construct(string $method, array $datas = []){
        $this->method = $method;
        $this->datas = $datas;
    }
}