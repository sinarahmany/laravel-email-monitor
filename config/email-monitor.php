<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Monitor Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the email monitoring is enabled.
    | When disabled, no email logs will be created.
    |
    */
    'enabled' => env('EMAIL_MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection to use for storing email logs.
    | If null, the default database connection will be used.
    |
    */
    'connection' => env('EMAIL_MONITOR_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The table name to use for storing email logs.
    |
    */
    'table' => 'email_logs',

    /*
    |--------------------------------------------------------------------------
    | Log Body Content
    |--------------------------------------------------------------------------
    |
    | Whether to log the email body content. Set to false to save storage space.
    |
    */
    'log_body' => env('EMAIL_MONITOR_LOG_BODY', true),

    /*
    |--------------------------------------------------------------------------
    | Log Metadata
    |--------------------------------------------------------------------------
    |
    | Whether to log additional metadata like user_id, ip_address, etc.
    |
    */
    'log_metadata' => env('EMAIL_MONITOR_LOG_METADATA', true),

    /*
    |--------------------------------------------------------------------------
    | Dashboard Route
    |--------------------------------------------------------------------------
    |
    | The route prefix for the email monitor dashboard.
    |
    */
    'route_prefix' => 'email-monitor',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to apply to the dashboard routes.
    | You can add authentication middleware here.
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Auto Cleanup
    |--------------------------------------------------------------------------
    |
    | Automatically clean up old email logs after specified days.
    | Set to null to disable auto cleanup.
    |
    */
    'auto_cleanup_days' => env('EMAIL_MONITOR_AUTO_CLEANUP_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Status Tracking
    |--------------------------------------------------------------------------
    |
    | Configure which email statuses to track.
    |
    */
    'track_statuses' => [
        'sending' => true,
        'sent' => true,
        'delivered' => true,
        'failed' => true,
        'bounced' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhooks for email status updates.
    |
    */
    'webhooks' => [
        'enabled' => env('EMAIL_MONITOR_WEBHOOKS_ENABLED', false),
        'url' => env('EMAIL_MONITOR_WEBHOOK_URL'),
        'secret' => env('EMAIL_MONITOR_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Filtering
    |--------------------------------------------------------------------------
    |
    | Configure which emails to monitor. You can exclude certain patterns.
    |
    */
    'filters' => [
        'exclude_patterns' => [
            // 'test@example.com',
            // '*@test.com',
        ],
        'include_patterns' => [
            // Only monitor emails matching these patterns
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure notifications for failed emails.
    |
    */
    'notifications' => [
        'enabled' => env('EMAIL_MONITOR_NOTIFICATIONS_ENABLED', false),
        'failed_email_threshold' => env('EMAIL_MONITOR_FAILED_THRESHOLD', 5),
        'notification_emails' => [
            // 'admin@example.com',
        ],
        'notify_on_failed' => env('EMAIL_MONITOR_NOTIFY_ON_FAILED', true),
        'notify_on_bounced' => env('EMAIL_MONITOR_NOTIFY_ON_BOUNCED', true),
        'notify_on_cleanup' => env('EMAIL_MONITOR_NOTIFY_ON_CLEANUP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Advanced Features
    |--------------------------------------------------------------------------
    |
    | Configure advanced monitoring features.
    |
    */
    'advanced' => [
        'track_opens' => env('EMAIL_MONITOR_TRACK_OPENS', false),
        'track_clicks' => env('EMAIL_MONITOR_TRACK_CLICKS', false),
        'rate_limiting' => [
            'enabled' => env('EMAIL_MONITOR_RATE_LIMITING', false),
            'max_emails_per_minute' => env('EMAIL_MONITOR_MAX_EMAILS_PER_MINUTE', 60),
        ],
        'real_time_updates' => env('EMAIL_MONITOR_REAL_TIME_UPDATES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Setup Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic setup features.
    |
    */
    'auto_setup' => [
        'enabled' => true,
        'create_migration' => true,
        'create_config' => true,
        'create_routes' => true,
        'run_migrations' => true, // Auto-run migrations by default
        'create_views' => true,
        'setup_environment' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Stuck Email Handling
    |--------------------------------------------------------------------------
    |
    | Configuration for handling stuck emails.
    |
    */
    'stuck_emails' => [
        'timeout_minutes' => env('EMAIL_MONITOR_TIMEOUT_MINUTES', 2),
        'auto_fix' => env('EMAIL_MONITOR_AUTO_FIX_STUCK', true),
        'cron_enabled' => env('EMAIL_MONITOR_CRON_ENABLED', false),
    ],
];

