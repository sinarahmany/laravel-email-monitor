<?php

namespace Laravel\EmailMonitor\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Laravel\EmailMonitor\Services\EmailMonitorService;

class EmailSentListener
{
    protected $emailMonitorService;

    public function __construct(EmailMonitorService $emailMonitorService)
    {
        $this->emailMonitorService = $emailMonitorService;
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        if (!config('email-monitor.enabled', true)) {
            return;
        }

        $this->emailMonitorService->logEmailSent($event);
    }
}

