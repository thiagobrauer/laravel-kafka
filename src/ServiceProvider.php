<?php

namespace ThiagoBrauer\LaravelKafka;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use ThiagoBrauer\LaravelKafka\Console\Commands\ConsumerCommand;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/laravel_kafka.php' => config_path('laravel_kafka.php')
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ConsumerCommand::class,
            ]);
        }
    }
}
