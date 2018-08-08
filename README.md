# Artisan command for backup Laravel application database


## Installation

You can install this package via composer using:

``` bash
composer require a2design-inc/laravel-db-backup
```

Register the provider (config/app.php) for Laravel < 5.5:

```PHP
'providers' => [
    ...
    /*
     * Package Service Providers...
     */
    A2design\DbBackup\DbBackupServiceProvider::class,
    ...
],
```

## Usage
For backup the database use next command
``` bash
php artisan db:backup
```

Also you can list all existed backups (for example before restore) using next command
``` bash
php artisan db:backups-list
```

And for restore backup use next command
``` bash
php artisan db:restore
```

By default this command will use latest backup, but you can provide filename of existed backup
``` bash
php artisan db:restore 2017-12-17.sql[.gz]
```

In this case using compression will be detected automatically.

## Scheduling

The commands can, like an other command, be scheduled in Laravel's console kernel.

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
   $schedule->command('db:backup')->daily()->at('00:00');
}
```

Of course, the schedules used in the code above are just an example. Adjust them to your own preferences.

## License

Licensed under The [MIT](LICENCE.md) License

Developed by [A2 Design Inc.](http://www.a2design.biz)
