<?php
namespace Bottel\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BottelProvider extends ServiceProvider
{
    public function register(){
    }

    public function boot(){
        $this->publishes([
            __DIR__.'/../routes/bottel.php' => base_path('routes/bottel.php'),
        ], 'routes');
        if($this->app->runningInConsole()){
        }else{
            Route::withoutMiddleware('api')
                ->middleware(\Bottel\Middleware\WebhookMiddleware::class)
                ->put('/', function(){
                    $this->loadRoutesFrom(base_path('routes/bottel.php'));
                });
        }
    }
}
