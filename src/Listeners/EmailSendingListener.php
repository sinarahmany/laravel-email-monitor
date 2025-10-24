<?php

namespace Laravel\EmailMonitor\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Laravel\EmailMonitor\Models\EmailLog;
use Laravel\EmailMonitor\Services\EmailMonitorService;

class EmailSendingListener
{
    protected $emailMonitorService;

    public function __construct(EmailMonitorService $emailMonitorService)
    {
        $this->emailMonitorService = $emailMonitorService;
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSending $event): void
    {
        if (!config('email-monitor.enabled', true)) {
            return;
        }

        $this->emailMonitorService->logEmailSending($event);
    }
}

