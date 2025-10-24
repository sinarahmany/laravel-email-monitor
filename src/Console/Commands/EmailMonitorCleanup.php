<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Laravel\EmailMonitor\Models\EmailLog;
use Laravel\EmailMonitor\Services\EmailMonitorService;
use Illuminate\Support\Facades\Notification;
use Laravel\EmailMonitor\Notifications\EmailMonitorNotification;

class EmailMonitorCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:cleanup 
                            {--days=90 : Number of days to keep logs}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--notify : Send notification about cleanup}
                            {--handle-stuck : Handle emails stuck in sending status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old email logs with advanced options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $notify = $this->option('notify');
        $handleStuck = $this->option('handle-stuck');
        
        // Handle stuck emails first if requested
        if ($handleStuck) {
            $this->handleStuckEmails();
        }
        
        $cutoffDate = now()->subDays($days);

        $query = EmailLog::where('created_at', '<', $cutoffDate);
        $count = $query->count();

        if ($count === 0) {
            $this->info("No email logs older than {$days} days found.");
            return 0;
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would delete {$count} email logs older than {$days} days.");
            $this->table(
                ['ID', 'To', 'Subject', 'Status', 'Created'],
                $query->limit(10)->get(['id', 'to', 'subject', 'status', 'created_at'])->toArray()
            );
            return 0;
        }

        if ($this->confirm("Are you sure you want to delete {$count} email logs?")) {
            $deletedCount = $query->delete();
            $this->info("Cleaned up {$deletedCount} email logs older than {$days} days.");

            if ($notify && config('email-monitor.notifications.enabled')) {
                $this->sendCleanupNotification($deletedCount, $days);
            }
        } else {
            $this->info("Cleanup cancelled.");
        }

        return 0;
    }

    /**
     * Handle stuck emails
     */
    protected function handleStuckEmails()
    {
        $emailMonitorService = app(EmailMonitorService::class);
        $stuckCount = $emailMonitorService->handleStuckEmails(2); // 2 minutes timeout
        
        if ($stuckCount > 0) {
            $this->info("Marked {$stuckCount} stuck emails as failed.");
        } else {
            $this->info("No stuck emails found.");
        }
    }

    /**
     * Send cleanup notification
     */
    protected function sendCleanupNotification($deletedCount, $days)
    {
        $notificationEmails = config('email-monitor.notifications.notification_emails', []);
        
        if (empty($notificationEmails)) {
            $this->warn("No notification emails configured.");
            return;
        }

        foreach ($notificationEmails as $email) {
            try {
                Notification::route('mail', $email)
                    ->notify(new EmailMonitorNotification(
                        (object) [
                            'message_id' => 'cleanup-' . now()->timestamp,
                            'to' => 'System',
                            'subject' => 'Email Logs Cleanup',
                            'formatted_status' => 'Cleaned',
                            'created_at' => now(),
                            'error_message' => null
                        ],
                        'cleanup'
                    ));
            } catch (\Exception $e) {
                $this->error("Failed to send notification to {$email}: " . $e->getMessage());
            }
        }
    }
}
