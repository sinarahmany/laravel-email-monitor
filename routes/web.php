<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Laravel\EmailMonitor\Http\Controllers\EmailMonitorController;
use Laravel\EmailMonitor\Http\Controllers\WebhookController;

Route::prefix('email-monitor')->name('email-monitor.')->group(function () {
    Route::get('/', [EmailMonitorController::class, 'index'])->name('index');
    Route::get('/{id}', [EmailMonitorController::class, 'show'])->name('show');
    Route::get('/api/statistics', [EmailMonitorController::class, 'statistics'])->name('statistics');
    Route::get('/api/recent', [EmailMonitorController::class, 'recent'])->name('recent');
    Route::post('/{id}/resend', [EmailMonitorController::class, 'resend'])->name('resend');
    Route::post('/{id}/mark-sent', [EmailMonitorController::class, 'markAsSent'])->name('mark-sent');
    Route::post('/fix-stuck', [EmailMonitorController::class, 'fixStuckEmails'])->name('fix-stuck');
    Route::delete('/{id}', [EmailMonitorController::class, 'destroy'])->name('destroy');
    
    // Webhook routes
    Route::post('/webhook', [WebhookController::class, 'handle'])->name('webhook');
});

// Test route for email sending
Route::get('/test-email', function () {
    Mail::raw('This is a test email', function ($message) {
        $message->to('test@example.com')->subject('Test Email');
    });
    
    return 'Test email sent! Check the email monitor dashboard.';
});

