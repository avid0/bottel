<?php
namespace Bottel;

use Bottel\Database\Models\BottelSymbolic;

class Symbolic {
    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $scheme;

    /**
     * @var int
     */
    public $port;

    /**
     * @var string
     */
    public $app;

    /**
     * @var string
     */
    public static $symbolic_path = "app/symbolic.php";

    /**
     * __construct
     *
     * @param  string $uri
     * @return void
     */
    public function __construct(string $uri = null){
        if($uri){
            $this->server($uri);
        }elseif(isset($_SERVER['SCRIPT_URI']) && isset($_SERVER['SERVER_ADDR']) && !in_array($_SERVER['SERVER_ADDR'], ['localhost', '127.0.0.1', '::1'])){
            $this->server(dirname(dirname($_SERVER['SCRIPT_URI'])));
        }
    }
    
    /**
     * initialized
     *
     * @return bool
     */
    public function initialized(): bool {
        return $this->host && $this->scheme && $this->port && $this->app;
    }
    
    /**
     * server
     *
     * @param  string $uri
     * @return Symbolic
     */
    public function server(string $uri): Symbolic {
        $parse = parse_url($uri);
        $this->host = $parse['host'] ?? null;
        $this->scheme = strtolower($parse['scheme'] ?? 'https');
        $this->port = $parse['port'] ?? null;
        if($this->port == 443 || $this->port == 80){
            $this->port = null;
        }
        $this->app = isset($parse['path']) ? rtrim($parse['path'], '/') : '/';
        return $this;
    }
    
    /**
     * url
     *
     * @param  string $path
     * @return string
     */
    public function url(string $path): string {
        $path = ltrim($path, '/');
        $port = $this->port ? ":{$this->port}" : '';
        $url = "{$this->scheme}://{$this->host}$port{$this->app}/$path";
        return $url;
    }
    
    /**
     * file
     *
     * @param  string $file
     * @param  ?string $filename
     * @param  ?string $mime_type
     * @param  ?bool $keep_alive_modifies
     * @param  ?bool $on_time
     * @return string
     */
    public function file(string $file, string $filename = null, string $mime_type = null, bool $keep_alive_modifies = false, bool $on_time = false): string {
        if(!$this->host){
            return false;
        }
        $path = realpath($file);
        if(!$path){
            return false;
        }
        $mtime = filemtime($path);
        $filename = $filename ?: basename($path);
        $mime_type = $mime_type ?: mime_content_type($path) ?: "application/octet-stream";
        $cache = BottelSymbolic::where('file', $path)
                                ->where('filename', $filename)
                                ->where('mime_type', $mime_type)
                                ->where('file_modified_at', $mtime)
                                ->where('keep_alive_modifies', $keep_alive_modifies)
                                ->where('on_time', $on_time)
                                ->first();
        if($cache){
            $key = $cache->key;
        }else{
            $key = bin2hex(random_bytes(16));
            BottelSymbolic::create([
                'key' => $key,
                'file' => $path,
                'filename' => $filename,
                'mime_type' => $mime_type,
                'file_modified_at' => $mtime,
                'keep_alive_modifies' => $keep_alive_modifies,
                'on_time' => $on_time,
            ]);
        }
        $url = $this->url(self::$symbolic_path);
        return "$url?k=$key";
    }
    
    /**
     * get
     *
     * @param  string $key
     * @return false|object
     */
    public function get(string $key): false|object {
        $symbol = BottelSymbolic::where('key', $key)->first();
        if($symbol){
            if(!file_exists($symbol->file)){
                $symbol->delete();
                return false;
            }
            if(!$symbol->keep_alive_modifies){
                $mtime = filemtime($symbol->file);
                if($symbol->file_modified_at < $mtime){
                    $symbol->delete();
                    return false;
                }
            }
            if($symbol->on_time){
                $symbol->delete();
            }
        }
        return $symbol;
    }
}

?>