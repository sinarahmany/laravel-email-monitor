<?php

namespace Laravel\EmailMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'to',
        'cc',
        'bcc',
        'from',
        'subject',
        'body',
        'status',
        'sent_at',
        'delivered_at',
        'failed_at',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by recipient
     */
    public function scopeToEmail($query, $email)
    {
        return $query->where('to', 'like', "%{$email}%");
    }

    /**
     * Scope for filtering by sender
     */
    public function scopeFromEmail($query, $email)
    {
        return $query->where('from', 'like', "%{$email}%");
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'sending' => 'badge-warning',
            'sent' => 'badge-info',
            'delivered' => 'badge-success',
            'failed' => 'badge-danger',
            'bounced' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get time since sent
     */
    public function getTimeSinceSentAttribute(): ?string
    {
        if (!$this->sent_at) {
            return null;
        }

        return $this->sent_at->diffForHumans();
    }

    /**
     * Get recipient list as array
     */
    public function getRecipientsAttribute(): array
    {
        $recipients = [];
        
        if ($this->to) {
            $recipients = array_merge($recipients, explode(', ', $this->to));
        }
        
        if ($this->cc) {
            $recipients = array_merge($recipients, explode(', ', $this->cc));
        }
        
        if ($this->bcc) {
            $recipients = array_merge($recipients, explode(', ', $this->bcc));
        }

        return array_filter($recipients);
    }

    /**
     * Mark email as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark email as bounced
     */
    public function markAsBounced(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'bounced',
            'failed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark email as sent (manual update)
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Check if email is stuck in sending status
     */
    public function isStuck(int $timeoutMinutes = null): bool
    {
        if ($timeoutMinutes === null) {
            $timeoutMinutes = config('email-monitor.stuck_emails.timeout_minutes', 2);
        }
        
        return $this->status === 'sending' && 
               $this->created_at->lt(now()->subMinutes($timeoutMinutes));
    }

    /**
     * Get stuck emails
     */
    public static function getStuckEmails(int $timeoutMinutes = null)
    {
        if ($timeoutMinutes === null) {
            $timeoutMinutes = config('email-monitor.stuck_emails.timeout_minutes', 2);
        }
        
        return static::where('status', 'sending')
            ->where('created_at', '<', now()->subMinutes($timeoutMinutes))
            ->get();
    }
}

