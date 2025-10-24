<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Laravel\EmailMonitor\Models\EmailLog;

class CleanupEmailLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:cleanup {--days=90 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old email logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = EmailLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Cleaned up {$deletedCount} email logs older than {$days} days.");

        return 0;
    }
}

