<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Laravel\EmailMonitor\Services\EmailMonitorService;

class EmailMonitorStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:stats {--days=30 : Number of days to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display email monitoring statistics';

    /**
     * Execute the console command.
     */
    public function handle(EmailMonitorService $emailMonitorService)
    {
        $days = $this->option('days');
        $statistics = $emailMonitorService->getStatistics($days);

        $this->info("Email Monitor Statistics (Last {$days} days)");
        $this->line('=====================================');
        $this->line("Total Emails: {$statistics['total_emails']}");
        $this->line("Sent Emails: {$statistics['sent_emails']}");
        $this->line("Failed Emails: {$statistics['failed_emails']}");
        $this->line("Pending Emails: {$statistics['pending_emails']}");

        if ($statistics['total_emails'] > 0) {
            $successRate = round(($statistics['sent_emails'] / $statistics['total_emails']) * 100, 2);
            $this->line("Success Rate: {$successRate}%");
        }

        return 0;
    }
}

