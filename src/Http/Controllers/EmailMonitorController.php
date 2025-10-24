<?php

namespace Laravel\EmailMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\EmailMonitor\Services\EmailMonitorService;
use Laravel\EmailMonitor\Models\EmailLog;

class EmailMonitorController extends Controller
{
    protected $emailMonitorService;

    public function __construct(EmailMonitorService $emailMonitorService)
    {
        $this->emailMonitorService = $emailMonitorService;
    }

    /**
     * Display the email monitoring dashboard
     */
    public function index(Request $request)
    {
        $days = $request->get('days', 30);
        $status = $request->get('status');
        $search = $request->get('search');

        $query = EmailLog::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('to', 'like', "%{$search}%")
                  ->orWhere('from', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $emailLogs = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = $this->emailMonitorService->getStatistics($days);

        return view('email-monitor::dashboard', compact(
            'emailLogs',
            'statistics',
            'days',
            'status',
            'search'
        ));
    }

    /**
     * Show details of a specific email
     */
    public function show($id)
    {
        $emailLog = EmailLog::findOrFail($id);

        return view('email-monitor::show', compact('emailLog'));
    }

    /**
     * Get email statistics as JSON
     */
    public function statistics(Request $request)
    {
        $days = $request->get('days', 30);
        $statistics = $this->emailMonitorService->getStatistics($days);

        return response()->json($statistics);
    }

    /**
     * Get recent email logs as JSON
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $logs = $this->emailMonitorService->getRecentLogs($limit);

        return response()->json($logs);
    }

    /**
     * Resend a failed email
     */
    public function resend($id)
    {
        $emailLog = EmailLog::findOrFail($id);

        if ($emailLog->status !== 'failed') {
            return redirect()->back()->with('error', 'Only failed emails can be resent.');
        }

        // Here you would implement the resend logic
        // This is a placeholder - you'd need to implement actual resending
        $emailLog->update([
            'status' => 'sending',
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        return redirect()->back()->with('success', 'Email has been queued for resending.');
    }

    /**
     * Delete an email log
     */
    public function destroy($id)
    {
        $emailLog = EmailLog::findOrFail($id);
        $emailLog->delete();

        return redirect()->route('email-monitor.index')
            ->with('success', 'Email log deleted successfully.');
    }

    /**
     * Fix stuck emails
     */
    public function fixStuckEmails()
    {
        $stuckCount = $this->emailMonitorService->handleStuckEmails(2); // 2 minutes timeout
        
        return redirect()->route('email-monitor.index')
            ->with('success', "Fixed {$stuckCount} stuck emails.");
    }

    /**
     * Manually mark an email as sent
     */
    public function markAsSent($id)
    {
        $emailLog = EmailLog::findOrFail($id);
        
        if ($emailLog->status !== 'sending') {
            return redirect()->back()->with('error', 'Only emails with "sending" status can be marked as sent.');
        }

        $emailLog->markAsSent();

        return redirect()->back()->with('success', 'Email marked as sent successfully.');
    }
}

