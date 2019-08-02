<?php

namespace A2design\DbBackup;

use A2design\DbBackup\Commands\DbBackupCommand;
use A2design\DbBackup\Commands\DbBackupsListCommand;
use A2design\DbBackup\Commands\DbRestoreCommand;
use Illuminate\Support\ServiceProvider;

class DbBackupServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
         $this->commands(
            [
                DbBackupCommand::class,
                DbBackupsListCommand::class,
                DbRestoreCommand::class,
            ]
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {}
}
