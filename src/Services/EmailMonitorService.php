<?php

namespace Laravel\EmailMonitor\Services;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Events\MessageFailed;
use Laravel\EmailMonitor\Models\EmailLog;
use Illuminate\Support\Str;

class EmailMonitorService
{
    /**
     * Log email when it's being sent
     */
    public function logEmailSending(MessageSending $event): void
    {
        $message = $event->message;
        $messageId = $message->getHeaders()->get('Message-ID');
        $messageIdValue = $messageId ? $messageId->getValue() : Str::uuid();

        EmailLog::create([
            'message_id' => $messageIdValue,
            'to' => $this->extractEmailAddresses($message->getTo()),
            'cc' => $this->extractEmailAddresses($message->getCc()),
            'bcc' => $this->extractEmailAddresses($message->getBcc()),
            'from' => $this->extractEmailAddresses($message->getFrom()),
            'subject' => $message->getSubject(),
            'body' => $this->extractBodyContent($message),
            'status' => 'sending',
            'sent_at' => null,
            'delivered_at' => null,
            'failed_at' => null,
            'error_message' => null,
            'metadata' => $this->extractMetadata($event),
        ]);
    }

    /**
     * Update email log when it's been sent
     */
    public function logEmailSent(MessageSent $event): void
    {
        $message = $event->message;
        $messageId = $message->getHeaders()->get('Message-ID');
        $messageIdValue = $messageId ? $messageId->getValue() : null;

        // Try to find the email log by Message-ID first
        if ($messageIdValue) {
            $emailLog = EmailLog::where('message_id', $messageIdValue)
                ->where('status', 'sending')
                ->first();
            
            if ($emailLog) {
                $emailLog->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                return;
            }
        }

        // Fallback: Find by matching email details if Message-ID doesn't work
        $to = $this->extractEmailAddresses($message->getTo());
        $from = $this->extractEmailAddresses($message->getFrom());
        $subject = $message->getSubject();

        if ($to && $from && $subject) {
            $emailLog = EmailLog::where('to', $to)
                ->where('from', $from)
                ->where('subject', $subject)
                ->where('status', 'sending')
                ->where('created_at', '>=', now()->subMinutes(5)) // Only match recent emails
                ->orderBy('created_at', 'desc')
                ->first();

            if ($emailLog) {
                $emailLog->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            }
        }
    }

    /**
     * Extract email addresses from message headers
     */
    protected function extractEmailAddresses($addresses): ?string
    {
        if (!$addresses) {
            return null;
        }

        $emails = [];
        foreach ($addresses as $address) {
            $emails[] = $address->getAddress();
        }

        return implode(', ', $emails);
    }

    /**
     * Extract body content from message
     */
    protected function extractBodyContent($message): ?string
    {
        $body = $message->getBody();
        
        if (!$body) {
            return null;
        }

        // If it's a MIME part, get the string content
        if (method_exists($body, 'toString')) {
            return $body->toString();
        }

        // If it's already a string, return it
        if (is_string($body)) {
            return $body;
        }

        // Fallback: try to convert to string
        return (string) $body;
    }

    /**
     * Extract metadata from the event
     */
    protected function extractMetadata(MessageSending $event): array
    {
        return [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get email statistics
     */
    public function getStatistics($days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_emails' => EmailLog::where('created_at', '>=', $startDate)->count(),
            'sent_emails' => EmailLog::where('status', 'sent')->where('created_at', '>=', $startDate)->count(),
            'failed_emails' => EmailLog::where('status', 'failed')->where('created_at', '>=', $startDate)->count(),
            'pending_emails' => EmailLog::where('status', 'sending')->where('created_at', '>=', $startDate)->count(),
            'daily_stats' => EmailLog::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, status')
                ->groupBy('date', 'status')
                ->orderBy('date')
                ->get(),
        ];
    }

    /**
     * Get recent email logs
     */
    public function getRecentLogs($limit = 50)
    {
        return EmailLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Log email when it fails to send
     */
    public function logEmailFailed(MessageFailed $event): void
    {
        $message = $event->message;
        $messageId = $message->getHeaders()->get('Message-ID');
        $messageIdValue = $messageId ? $messageId->getValue() : null;

        // Try to find the email log by Message-ID first
        if ($messageIdValue) {
            $emailLog = EmailLog::where('message_id', $messageIdValue)
                ->where('status', 'sending')
                ->first();
            
            if ($emailLog) {
                $emailLog->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => $this->extractErrorMessage($event->exception),
                ]);
                return;
            }
        }

        // Fallback: Find by matching email details if Message-ID doesn't work
        $to = $this->extractEmailAddresses($message->getTo());
        $from = $this->extractEmailAddresses($message->getFrom());
        $subject = $message->getSubject();

        if ($to && $from && $subject) {
            $emailLog = EmailLog::where('to', $to)
                ->where('from', $from)
                ->where('subject', $subject)
                ->where('status', 'sending')
                ->where('created_at', '>=', now()->subMinutes(5)) // Only match recent emails
                ->orderBy('created_at', 'desc')
                ->first();

            if ($emailLog) {
                $emailLog->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => $this->extractErrorMessage($event->exception),
                ]);
            }
        }
    }

    /**
     * Extract error message from exception
     */
    protected function extractErrorMessage($exception): string
    {
        if (!$exception) {
            return 'Unknown error occurred';
        }

        // Get the exception message
        $message = $exception->getMessage();
        
        // If it's a Swift_TransportException, try to extract more details
        if (strpos($message, 'Authentication failed') !== false) {
            return 'SMTP Authentication failed - check your email credentials';
        }
        
        if (strpos($message, 'Connection could not be established') !== false) {
            return 'SMTP Connection failed - check your SMTP settings';
        }
        
        if (strpos($message, 'SSL') !== false || strpos($message, 'TLS') !== false) {
            return 'SSL/TLS connection error - check your encryption settings';
        }

        // Return the original message if no specific pattern matches
        return $message;
    }

    /**
     * Handle stuck emails that have been in 'sending' status for too long
     */
    public function handleStuckEmails($timeoutMinutes = null): int
    {
        // Use config value if no timeout provided
        if ($timeoutMinutes === null) {
            $timeoutMinutes = config('email-monitor.stuck_emails.timeout_minutes', 2);
        }
        
        $cutoffTime = now()->subMinutes($timeoutMinutes);
        
        $stuckEmails = EmailLog::where('status', 'sending')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        $updatedCount = 0;
        
        foreach ($stuckEmails as $email) {
            // Mark as failed with a timeout message
            $email->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => 'Email timed out - stuck in sending status for more than ' . $timeoutMinutes . ' minutes',
            ]);
            $updatedCount++;
        }

        return $updatedCount;
    }
}

