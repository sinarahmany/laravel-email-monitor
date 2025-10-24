<?php

namespace Laravel\EmailMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\EmailMonitor\Models\EmailLog;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle webhook notifications for email status updates
     */
    public function handle(Request $request)
    {
        if (!config('email-monitor.webhooks.enabled', false)) {
            return response()->json(['error' => 'Webhooks not enabled'], 403);
        }

        $secret = config('email-monitor.webhooks.secret');
        if ($secret && !$this->verifyWebhookSignature($request, $secret)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();
        
        try {
            $this->processWebhookData($data);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Email Monitor Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Process webhook data and update email status
     */
    protected function processWebhookData(array $data)
    {
        $messageId = $data['message_id'] ?? null;
        $status = $data['status'] ?? null;
        $reason = $data['reason'] ?? null;

        if (!$messageId || !$status) {
            throw new \Exception('Missing required fields: message_id and status');
        }

        $emailLog = EmailLog::where('message_id', $messageId)->first();
        
        if (!$emailLog) {
            throw new \Exception('Email log not found for message_id: ' . $messageId);
        }

        switch ($status) {
            case 'delivered':
                $emailLog->markAsDelivered();
                break;
            case 'bounced':
                $emailLog->markAsBounced($reason);
                break;
            case 'failed':
                $emailLog->markAsFailed($reason);
                break;
            default:
                throw new \Exception('Unknown status: ' . $status);
        }
    }

    /**
     * Verify webhook signature
     */
    protected function verifyWebhookSignature(Request $request, string $secret): bool
    {
        $signature = $request->header('X-Webhook-Signature');
        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($expectedSignature, $signature);
    }
}
