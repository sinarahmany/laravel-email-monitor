<?php

/**
 * Email Monitor One-Click Setup Test Script
 * 
 * This script tests the complete one-click setup functionality
 * Run this after installing the package to verify everything works
 */

echo "🧪 Testing Email Monitor One-Click Setup...\n\n";

// Test 1: Check if package is installed
echo "1. Checking package installation...\n";
$composerLock = file_exists(__DIR__ . '/../composer.lock');
if ($composerLock) {
    $composerContent = file_get_contents(__DIR__ . '/../composer.lock');
    $packageInstalled = strpos($composerContent, 'sinarahmany/laravel-email-monitor') !== false;
    if ($packageInstalled) {
        echo "   ✅ Package is installed\n";
    } else {
        echo "   ❌ Package not found in composer.lock\n";
    }
} else {
    echo "   ⚠️  composer.lock not found\n";
}

// Test 2: Check if service provider is registered
echo "\n2. Checking service provider registration...\n";
$configApp = file_exists(__DIR__ . '/../config/app.php');
if ($configApp) {
    $appContent = file_get_contents(__DIR__ . '/../config/app.php');
    $providerRegistered = strpos($appContent, 'Laravel\\EmailMonitor\\EmailMonitorServiceProvider') !== false;
    if ($providerRegistered) {
        echo "   ✅ Service provider is registered\n";
    } else {
        echo "   ⚠️  Service provider not found in config/app.php (auto-discovery should handle this)\n";
    }
} else {
    echo "   ⚠️  config/app.php not found\n";
}

// Test 3: Check if configuration file exists
echo "\n3. Checking configuration file...\n";
$configFile = file_exists(__DIR__ . '/../config/email-monitor.php');
if ($configFile) {
    echo "   ✅ Configuration file exists\n";
} else {
    echo "   ❌ Configuration file not found\n";
}

// Test 4: Check if migration file exists
echo "\n4. Checking migration file...\n";
$migrationFile = file_exists(__DIR__ . '/../database/migrations/2024_01_01_000000_create_email_logs_table.php');
if ($migrationFile) {
    echo "   ✅ Migration file exists\n";
} else {
    echo "   ❌ Migration file not found\n";
}

// Test 5: Check if views are published
echo "\n5. Checking view files...\n";
$viewsDir = is_dir(__DIR__ . '/../resources/views/vendor/email-monitor');
if ($viewsDir) {
    echo "   ✅ Views are published\n";
} else {
    echo "   ⚠️  Views not published (will be auto-loaded from package)\n";
}

// Test 6: Check if routes are loaded
echo "\n6. Checking routes...\n";
$routesFile = file_exists(__DIR__ . '/../routes/web.php');
if ($routesFile) {
    $routesContent = file_get_contents(__DIR__ . '/../routes/web.php');
    $routesAdded = strpos($routesContent, 'email-monitor') !== false;
    if ($routesAdded) {
        echo "   ✅ Routes are added to web.php\n";
    } else {
        echo "   ⚠️  Routes not found in web.php (will be auto-loaded from package)\n";
    }
} else {
    echo "   ⚠️  routes/web.php not found\n";
}

// Test 7: Check environment variables
echo "\n7. Checking environment variables...\n";
$envFile = file_exists(__DIR__ . '/../.env');
if ($envFile) {
    $envContent = file_get_contents(__DIR__ . '/../.env');
    $envConfigured = strpos($envContent, 'EMAIL_MONITOR') !== false;
    if ($envConfigured) {
        echo "   ✅ Environment variables are configured\n";
    } else {
        echo "   ⚠️  Environment variables not found (will use defaults)\n";
    }
} else {
    echo "   ⚠️  .env file not found\n";
}

echo "\n🎯 Setup Test Complete!\n";
echo "\nNext steps:\n";
echo "1. Run: php artisan email-monitor:install\n";
echo "2. Visit: /email-monitor to access the dashboard\n";
echo "3. Visit: /test-email to send a test email\n";
echo "4. Check the dashboard to see the monitored email\n";
