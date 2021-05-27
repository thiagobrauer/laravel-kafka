<?php

namespace ThiagoBrauer\LaravelKafka;

use Illuminate\Support\ServiceProvider;
use ThiagoBrauer\LaravelKafka\Console\Commands\ConsumerCommand;

class LaravelKafkaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
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
