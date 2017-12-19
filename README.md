# Artisan command for backup Laravel application database


## Installation

You can install this package via composer using:

``` bash
composer require a2design-inc/laravel-db-backup
```

Register the provider (config/app.php):

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
``` bash
php artisan db:backup
```


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