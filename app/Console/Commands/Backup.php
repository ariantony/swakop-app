<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Throwable;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup MySQL';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = now()->format('Y-m-d_H-i-s');

        $p = new Process([
            'mysqldump',
            '-u' . config('database.connections.mysql.username'),
            '-p' . config('database.connections.mysql.password'),
            '--databases',
            config('database.connections.mysql.database')
        ]);

        $p->run();
        
        try {
            File::put(sprintf('D:\\backup-swakop\\%s.sql', $date), $p->getOutput());

            return 0;
        } catch (Throwable $e) {
            Log::error('backup', [
                'exception' => $e,
            ]);
            
            return 1;
        }
    }
}
