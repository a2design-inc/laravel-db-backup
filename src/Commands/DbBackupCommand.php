<?php

namespace A2design\DbBackup\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DbBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup current application database and remove old backups';

    /**
     * @var null|string
     */
    protected $backupsDir = null;

    /**
     * @var Filesystem|null
     */
    protected $fileSystem = null;

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = [
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD') ,
            'name' => env('DB_DATABASE'),
            'dest' => $this->backupsDir . date('Y-m-d') . '.sql',
            'gzip' => true,
        ];

        $command = $this->getCommand($config);
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->cleanup();
    }

    /**
     * @param $config
     * @return string
     */
    private function getCommand($config)
    {
        $cmd = "mysqldump --hex-blob";
        if (!empty($config['host'])) {
            $cmd .= " -h{$config['host']}";
        }
        if (!empty($config['port'])) {
            $cmd .= " -P{$config['port']}";
        }
        if (!empty($config['user'])) {
            $cmd .= " -u{$config['user']}";
        }
        if (!empty($config['pass'])) {
            $cmd .= " -p{$config['pass']}";
        }

        $cmd .= " {$config['name']}";

        if ($config['gzip']) {
            $cmd .= " | gzip > {$config['dest']}.gz";
        } else {
            $cmd .= " > {$config['dest']}";
        }

        return $cmd;
    }

    /**
     * @return void
     */
    private function cleanup()
    {
        $dates = $this->getDates();

        $finder = new Finder();
        $finder->files()->in(storage_path('backups'));

        foreach ($finder as $file) {
            $tmp = explode('.', $file->getFilename());
            if (!in_array($tmp[0], $dates)) {
                $this->fileSystem->delete($file->getRealPath());
            }
        }
    }

    /**
     * @return array
     */
    private function getDates()
    {
        $allowedDates = [
            date('Y-m-d'),
        ];
        for ($i = 1; $i <= 7; $i++) {
            $allowedDates[] = date('Y-m-d', strtotime('-' . $i . ' day'));
        }
        for ($i = 1; $i <= 12; $i++) {
            $date = date('Y-m-01', strtotime('-' . $i . ' month'));
            if (!in_array($date, $allowedDates)) {
                $allowedDates[] = $date;
            }
        }

        return $allowedDates;
    }
}
