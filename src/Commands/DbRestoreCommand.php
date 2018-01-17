<?php

namespace A2design\DbBackup\Commands;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DbRestoreCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {backup=last}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from backup';

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
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        $backup = $this->argument('backup');
        $backups = $this->getBackups();
        if ($backup == 'last') {
            $backup = end($backups);
        } else {
            if (!in_array($backup, $backups)) {
                throw new \Exception('Backup file does not exists.');
            }
        }

        $this->config['file'] = $this->backupsDir . $backup;

        $this->checkIsCompressed();

        $command = $this->getCommand($this->config);
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * @return void
     */
    private function checkIsCompressed()
    {
        $this->config['gzip'] = $this->fileSystem->extension($this->config['file']) == 'gz';
    }

    /**
     * @param $config
     * @return string
     */
    private function getCommand($config)
    {
        $cmd = "mysql";
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
            $cmd = "gunzip < {$config['file']} | {$cmd}";
        } else {
            $cmd .= " < {$config['file']}";
        }

        return $cmd;
    }

    private function getBackups()
    {
        $backups = [];

        $finder = new Finder();
        $finder->files()->in($this->backupsDir);
        foreach ($finder as $file) {
            $backups[] = $file->getFilename();
        }

        asort($backups);

        return $backups;
    }
}
