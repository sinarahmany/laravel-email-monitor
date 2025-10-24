<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Laravel\EmailMonitor\Services\EmailMonitorService;

class CheckStuckEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:check-stuck {--timeout=2 : Timeout in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for and fix stuck emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeout = $this->option('timeout');
        
        $emailMonitorService = app(EmailMonitorService::class);
        $stuckCount = $emailMonitorService->handleStuckEmails($timeout);
        
        if ($stuckCount > 0) {
            $this->info("Marked {$stuckCount} stuck emails as failed.");
        } else {
            $this->info("No stuck emails found.");
        }
        
        return 0;
    }
}
