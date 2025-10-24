# Laravel Email Monitor

[![Latest Version](https://img.shields.io/badge/version-0.1.0-blue.svg)](https://github.com/sinarahmany/laravel-email-monitor)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-9.x%20%7C%2010.x%20%7C%2011.x%20%7C%2012.x-red.svg)](https://laravel.com)

A comprehensive Laravel package to monitor all outgoing emails with tracking, logging, and dashboard capabilities. Built with ❤️ by [Sina Rahmannejad](https://sinarahmannejad.com).

## ✨ Features

- 📧 **Automatic Email Tracking**: Monitors all outgoing emails automatically
- 📊 **Dashboard Interface**: Beautiful web interface to view email statistics and logs
- 🔍 **Advanced Filtering**: Filter emails by status, date range, recipient, and more
- 📈 **Statistics & Analytics**: Detailed statistics and analytics for email performance
- 🔄 **Resend Failed Emails**: Easy resending of failed emails
- 🗂️ **Email Details**: View complete email details including body, headers, and metadata
- ⚙️ **Zero Configuration**: Works out-of-the-box with sensible defaults
- 🧹 **Auto Cleanup**: Automatic cleanup of old email logs
- 🔔 **Webhook Support**: Webhook notifications for email status updates
- 🎯 **Status Tracking**: Track sending, sent, delivered, failed, and bounced statuses
- 🚀 **One-Click Setup**: Install and configure with a single command

## 🚀 Quick Start (One-Click Setup)

### 1. Install the Package
```bash
composer require sinarahmany/laravel-email-monitor
```

### 2. One-Click Installation
```bash
php artisan email-monitor:install
```

That's it! 🎉 The package will automatically:
- ✅ Publish all assets (config, migrations, views)
- ✅ Create the database migration
- ✅ Run migrations automatically
- ✅ Set up routes
- ✅ Configure environment variables
- ✅ Verify installation

### 3. Access the Dashboard
Visit: `http://your-app.com/email-monitor`

### 4. Test Email Monitoring
Visit: `http://your-app.com/test-email` to send a test email and see it in the dashboard

## 🔧 Alternative Setup Methods

### Quick Setup (Legacy)
```bash
php artisan email-monitor:setup
```

### Force Reinstall
```bash
php artisan email-monitor:install --force
```

### Skip Database Setup
```bash
php artisan email-monitor:install --skip-migrate
```

## 📋 Manual Installation (Optional)

If you prefer manual control:

### 1. Install the Package
```bash
composer require sinarahmany/laravel-email-monitor
```

### 2. Publish Configuration
```bash
php artisan vendor:publish --tag=email-monitor-config
```

### 3. Publish Migrations
```bash
php artisan vendor:publish --tag=email-monitor-migrations
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Publish Views (Optional)
```bash
php artisan vendor:publish --tag=email-monitor-views
```

## ⚙️ Configuration (Optional)

The package works out-of-the-box with sensible defaults. All configuration is optional!

### Environment Variables (Optional)

Add these variables to your `.env` file only if you want to customize:

```env
# Enable/disable email monitoring (default: true)
EMAIL_MONITOR_ENABLED=true

# Database connection (optional, uses default if not set)
EMAIL_MONITOR_CONNECTION=mysql

# Log email body content (default: true)
EMAIL_MONITOR_LOG_BODY=true

# Log metadata (user_id, ip_address, etc.) (default: true)
EMAIL_MONITOR_LOG_METADATA=true

# Auto cleanup old logs after X days (default: 90)
EMAIL_MONITOR_AUTO_CLEANUP_DAYS=90

# Stuck email timeout in minutes (default: 2)
EMAIL_MONITOR_TIMEOUT_MINUTES=2

# Webhook configuration (optional)
EMAIL_MONITOR_WEBHOOKS_ENABLED=false
EMAIL_MONITOR_WEBHOOK_URL=
EMAIL_MONITOR_WEBHOOK_SECRET=

# Notification configuration (optional)
EMAIL_MONITOR_NOTIFICATIONS_ENABLED=false
EMAIL_MONITOR_FAILED_THRESHOLD=5
```

### Configuration File

The package includes a comprehensive configuration file at `config/email-monitor.php` with the following options:

- **enabled**: Enable/disable email monitoring
- **connection**: Database connection to use
- **table**: Table name for email logs
- **log_body**: Whether to log email body content
- **log_metadata**: Whether to log additional metadata
- **route_prefix**: Dashboard route prefix
- **middleware**: Middleware for dashboard routes
- **auto_cleanup_days**: Auto cleanup configuration
- **track_statuses**: Which statuses to track
- **webhooks**: Webhook configuration
- **filters**: Email filtering options
- **notifications**: Notification settings

## Usage

### Dashboard Access

Once installed, you can access the email monitor dashboard at:

```
http://your-app.com/email-monitor
```

### API Endpoints

The package provides several API endpoints:

- `GET /email-monitor/api/statistics` - Get email statistics
- `GET /email-monitor/api/recent` - Get recent email logs

### Programmatic Access

You can also access email logs programmatically:

```php
use Laravel\EmailMonitor\Models\EmailLog;
use Laravel\EmailMonitor\Services\EmailMonitorService;

// Get email logs
$emailLogs = EmailLog::where('status', 'sent')->get();

// Get statistics
$emailMonitorService = app(EmailMonitorService::class);
$statistics = $emailMonitorService->getStatistics(30); // Last 30 days

// Get recent logs
$recentLogs = $emailMonitorService->getRecentLogs(10);
```

### Model Methods

The `EmailLog` model provides several useful methods:

```php
// Get formatted status
$emailLog->formatted_status; // "Sent"

// Get status badge class
$emailLog->status_badge_class; // "badge-success"

// Get time since sent
$emailLog->time_since_sent; // "2 hours ago"

// Get recipients as array
$emailLog->recipients; // ['user@example.com', 'admin@example.com']

// Mark as delivered
$emailLog->markAsDelivered();

// Mark as failed
$emailLog->markAsFailed('SMTP timeout');

// Mark as bounced
$emailLog->markAsBounced('Invalid email address');
```

## Dashboard Features

### Statistics Overview

The dashboard provides comprehensive statistics:

- Total emails sent
- Successfully sent emails
- Pending emails
- Failed emails
- Daily statistics

### Email Logs Table

View all email logs with:

- Status badges
- Recipient information
- Sender information
- Subject lines
- Sent timestamps
- Action buttons (view, resend, delete)

### Filtering and Search

- Filter by status (sending, sent, delivered, failed, bounced)
- Search by recipient, sender, or subject
- Date range filtering
- Pagination support

### Email Details

View detailed information for each email:

- Complete email headers
- Full email body
- Timing information
- Error messages (if any)
- Metadata (user_id, ip_address, etc.)
- Action buttons (resend, delete)

## Advanced Features

### Webhook Support

Configure webhooks to receive real-time notifications:

```php
// In your webhook handler
public function handleEmailStatusUpdate($data)
{
    // Handle email status update
    $emailLog = EmailLog::where('message_id', $data['message_id'])->first();
    
    if ($data['status'] === 'delivered') {
        $emailLog->markAsDelivered();
    } elseif ($data['status'] === 'bounced') {
        $emailLog->markAsBounced($data['reason']);
    }
}
```

### Custom Middleware

Add authentication or other middleware to the dashboard:

```php
// In config/email-monitor.php
'middleware' => ['web', 'auth', 'admin'],
```

### Email Filtering

Configure which emails to monitor:

```php
// In config/email-monitor.php
'filters' => [
    'exclude_patterns' => [
        'test@example.com',
        '*@test.com',
    ],
    'include_patterns' => [
        // Only monitor emails matching these patterns
    ],
],
```

### Auto Cleanup

Automatically clean up old email logs:

```php
// In config/email-monitor.php
'auto_cleanup_days' => 90, // Clean up logs older than 90 days
```

## Troubleshooting

### Common Issues

1. **Emails not being logged**: Check if `EMAIL_MONITOR_ENABLED=true` in your `.env` file
2. **Dashboard not accessible**: Ensure the package is properly installed and routes are registered
3. **Database errors**: Make sure migrations have been run

### Debug Mode

Enable debug mode to see detailed logging:

```php
// In config/email-monitor.php
'debug' => env('EMAIL_MONITOR_DEBUG', false),
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please open an issue on GitHub or contact the maintainers.

## Changelog

### Version 0.2.0
- Initial release
- Basic email monitoring functionality
- Dashboard interface
- Statistics and analytics
- Email filtering and search
- Resend functionality
- Auto cleanup
- Webhook support

