<?php

namespace A2design\DbBackup\Commands;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DbBackupCommand extends BaseCommand
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $command = $this->getCommand($this->config);
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
            $cmd .= " -p'{$config['pass']}'";
        }

        $cmd .= " {$config['name']}";

        if ($config['gzip']) {
            $cmd .= " | gzip > {$config['file']}";
        } else {
            $cmd .= " > {$config['file']}";
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
        $finder->files()->in($this->backupsDir);

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
