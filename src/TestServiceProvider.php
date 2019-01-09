<?php

namespace Wangliang\Web;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * WEB
         */  
        $this->publishes([
            realpath(__DIR__ . '/Web/Controller') => app_path('Http/Controllers/Web'),
        ]);

        //模型
        $this->publishes([
            realpath(__DIR__ . '/Web/Models') => app_path('Models/Web'),
        ]);

        //路由
        $this->publishes([
            realpath(__DIR__ . '/Web/Routes') => base_path('routes/Web'),
        ]);

        //验证类
        $this->publishes([
            realpath(__DIR__ . '/Web/Requests') => app_path('Http/Requests/Web'),
        ]);
        
        //配置文件
        if(file_exists(config_path('constants.php'))==false){
            $this->publishes([
                __DIR__.'/config/constants.php' => config_path('constants.php'),
            ]);
        }

        //迁移文件
        $this->publishes([
            realpath(__DIR__ . '/database/migrations') => base_path('/database/migrations'),
        ]);

        //视图文件
        $this->publishes([
            realpath(__DIR__ . '/Web/Views') => base_path('resources\views\Web'),
        ]);

        //样式文件
        $this->publishes([
            realpath(__DIR__ . '/Web/web') => public_path('web'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('web', function ($app) {
            return new web();
        });
    }
}
