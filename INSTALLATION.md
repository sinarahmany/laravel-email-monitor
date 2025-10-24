# Email Monitor - One-Click Installation Guide

## 🚀 Quick Installation (Recommended)

### Step 1: Install the Package
```bash
composer require sinarahmany/laravel-email-monitor
```

### Step 2: One-Click Setup
```bash
php artisan email-monitor:install
```

**That's it!** 🎉 Your email monitor is now ready to use.

## 🔧 What the One-Click Setup Does

The `email-monitor:install` command automatically:

1. **Publishes Assets**
   - ✅ Configuration file (`config/email-monitor.php`)
   - ✅ Migration files (`database/migrations/`)
   - ✅ View files (`resources/views/vendor/email-monitor/`)

2. **Sets Up Database**
   - ✅ Creates migrations table (if needed)
   - ✅ Runs migrations automatically
   - ✅ Creates `email_logs` table

3. **Configures Environment**
   - ✅ Adds email monitor variables to `.env`
   - ✅ Sets up default configuration

4. **Sets Up Routes**
   - ✅ Adds redirect route to `routes/web.php`
   - ✅ Loads package routes automatically

5. **Verifies Installation**
   - ✅ Checks all components are working
   - ✅ Reports any issues found

## 🎯 Access Your Dashboard

After installation, you can access:

- **Dashboard**: `http://your-app.com/email-monitor`
- **Test Email**: `http://your-app.com/test-email`

## 🔄 Alternative Installation Methods

### Legacy Setup (Still Available)
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

## 🧪 Testing Your Installation

### 1. Run the Test Script
```bash
php test-setup.php
```

### 2. Send a Test Email
Visit: `http://your-app.com/test-email`

### 3. Check the Dashboard
Visit: `http://your-app.com/email-monitor`

## 🛠️ Troubleshooting

### Common Issues and Solutions

#### Issue: "Class not found" errors
**Solution**: Clear caches and run package discovery
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan package:discover
```

#### Issue: "Table doesn't exist" errors
**Solution**: Run migrations manually
```bash
php artisan migrate
```

#### Issue: "Route not found" errors
**Solution**: Check if routes are loaded
```bash
php artisan route:list | grep email-monitor
```

#### Issue: "View not found" errors
**Solution**: Publish views manually
```bash
php artisan vendor:publish --tag=email-monitor-views
```

### Manual Setup (If Auto-Setup Fails)

If the one-click setup fails, you can set up manually:

```bash
# 1. Publish all assets
php artisan vendor:publish --tag=email-monitor-config
php artisan vendor:publish --tag=email-monitor-migrations
php artisan vendor:publish --tag=email-monitor-views

# 2. Run migrations
php artisan migrate

# 3. Add routes to routes/web.php
Route::get('/email-monitor', function () {
    return redirect()->route('email-monitor.index');
});

# 4. Configure .env file
echo "EMAIL_MONITOR_ENABLED=true" >> .env
echo "EMAIL_MONITOR_TIMEOUT_MINUTES=2" >> .env
```

## 📋 Verification Checklist

After installation, verify these components:

- [ ] Configuration file exists: `config/email-monitor.php`
- [ ] Migration file exists: `database/migrations/2024_01_01_000000_create_email_logs_table.php`
- [ ] Database table exists: `email_logs`
- [ ] Routes are loaded: `php artisan route:list | grep email-monitor`
- [ ] Dashboard accessible: `/email-monitor`
- [ ] Test email works: `/test-email`

## 🎉 Success!

If everything is working correctly, you should see:

1. **Dashboard loads** at `/email-monitor`
2. **Test email sends** at `/test-email`
3. **Email appears** in the dashboard
4. **Statistics show** in the dashboard

## 📚 Next Steps

1. **Configure Email Settings**: Update your `.env` file with your email configuration
2. **Customize Dashboard**: Modify views in `resources/views/vendor/email-monitor/`
3. **Set Up Notifications**: Configure email notifications for failed emails
4. **Monitor Emails**: Start sending emails and watch them in the dashboard

## 🆘 Need Help?

If you encounter any issues:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Run the test script: `php test-setup.php`
3. Try manual setup steps
4. Check the documentation: `README.md`

---

**Built with ❤️ by [Sina Rahmannejad](https://sinarahmannejad.com)**
