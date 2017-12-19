<?php

namespace A2design\DbBackup\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class BaseCommand extends Command
{
    /**
     * @var null|string
     */
    protected $backupsDir = null;

    /**
     * @var Filesystem|null
     */
    protected $fileSystem = null;

    protected $config = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->fileSystem = new Filesystem();
        $this->backupsDir = storage_path('backups') . DIRECTORY_SEPARATOR;
        if (!$this->fileSystem->exists($this->backupsDir)) {
            $this->fileSystem->makeDirectory($this->backupsDir);
        }

        $compress = true;

        $this->config = [
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD') ,
            'name' => env('DB_DATABASE'),
            'file' => $this->backupsDir . date('Y-m-d') . '.sql' . ($compress ? '.gz' : ''),
            'gzip' => $compress,
        ];
    }
}
