<?php

namespace Laravel\EmailMonitor\Listeners;

use Illuminate\Mail\Events\MessageFailed;
use Laravel\EmailMonitor\Services\EmailMonitorService;

class EmailFailedListener
{
    protected $emailMonitorService;

    public function __construct(EmailMonitorService $emailMonitorService)
    {
        $this->emailMonitorService = $emailMonitorService;
    }

    public function handle(MessageFailed $event): void
    {
        if (!config('email-monitor.enabled', true)) {
            return;
        }

        $this->emailMonitorService->logEmailFailed($event);
    }
}
