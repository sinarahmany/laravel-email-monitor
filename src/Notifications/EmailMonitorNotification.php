<?php

namespace Laravel\EmailMonitor\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailMonitorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $emailLog;
    protected $type;

    public function __construct($emailLog, $type = 'failed')
    {
        $this->emailLog = $emailLog;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = match($this->type) {
            'failed' => 'Email Failed to Send',
            'bounced' => 'Email Bounced',
            'delivered' => 'Email Delivered',
            default => 'Email Status Update'
        };

        return (new MailMessage)
            ->subject($subject)
            ->line("Email ID: {$this->emailLog->message_id}")
            ->line("To: {$this->emailLog->to}")
            ->line("Subject: {$this->emailLog->subject}")
            ->line("Status: {$this->emailLog->formatted_status}")
            ->line("Time: {$this->emailLog->created_at->format('Y-m-d H:i:s')}")
            ->when($this->emailLog->error_message, function ($message) {
                return $message->line("Error: {$this->emailLog->error_message}");
            })
            ->action('View Dashboard', url('/email-monitor'));
    }
}
