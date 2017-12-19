<?php

namespace A2design\DbBackup\Commands;

use Symfony\Component\Finder\Finder;

class DbBackupsListCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backups-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of created and stored backups';

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
        $finder = new Finder();
        $finder->files()->in($this->backupsDir);
        foreach ($finder as $file) {
            $this->output->writeln($file->getFilename());
        }
    }
}
