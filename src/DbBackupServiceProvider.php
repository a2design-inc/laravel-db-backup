<?php

namespace A2design\DbBackup;

use Illuminate\Support\ServiceProvider;

class DbBackupServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            [
                \A2design\DbBackup\Commands\DbBackupCommand::class
            ]
        );
    }
}