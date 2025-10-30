<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AutoSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:setup 
                            {--force : Force setup even if already configured}
                            {--skip-migrate : Skip running migrations automatically}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-click setup for Email Monitor package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up Email Monitor package...');
        $this->newLine();

        // Check if already configured
        if (!$this->option('force') && $this->isAlreadyConfigured()) {
            $this->warn('Email Monitor is already configured!');
            $this->info('Use --force to reconfigure.');
            return 0;
        }

        // Step 1: Publish configuration
        $this->info('📝 Publishing configuration...');
        $this->publishConfig();

        // Step 2: Publish migrations
        $this->info('🗄️ Publishing migrations...');
        $this->publishMigrations();

        // Step 3: Publish views
        $this->info('🎨 Publishing views...');
        $this->publishViews();

        // Step 4: Run migrations (default behavior)
        if (!$this->option('skip-migrate')) {
            $this->info('⚡ Running migrations...');
            $this->runMigrations();
        }

        // Step 5: Create sample environment variables
        $this->info('🔧 Setting up environment variables...');
        $this->setupEnvironmentVariables();

        // Step 6: Create sample routes
        $this->info('🛣️ Setting up routes...');
        $this->setupRoutes();

        // Step 7: Verify installation
        $this->info('🔍 Verifying installation...');
        $this->verifyInstallation();

        $this->newLine();
        $this->info('✅ Email Monitor setup complete!');
        $this->newLine();
        
        $this->info('🎉 Your email monitor is ready to use!');
        $this->line('📊 Dashboard: /email-monitor');
        $this->line('📧 Test email: /test-email');
        $this->line('⚙️  Configure email settings in your .env file');
        $this->line('📚 Documentation: https://github.com/your-repo/email-monitor');
        
        return 0;
    }

    /**
     * Check if already configured
     */
    protected function isAlreadyConfigured(): bool
    {
        return file_exists(config_path('email-monitor.php')) &&
               file_exists(database_path('migrations/2024_01_01_000000_create_email_logs_table.php'));
    }

    /**
     * Publish configuration
     */
    protected function publishConfig(): void
    {
        try {
            Artisan::call('vendor:publish', [
                '--tag' => 'email-monitor-config',
                '--force' => $this->option('force')
            ]);
            $this->line('   ✓ Configuration published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish configuration: ' . $e->getMessage());
        }
    }

    /**
     * Publish migrations
     */
    protected function publishMigrations(): void
    {
        try {
            Artisan::call('vendor:publish', [
                '--tag' => 'email-monitor-migrations',
                '--force' => $this->option('force')
            ]);
            $this->line('   ✓ Migrations published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish migrations: ' . $e->getMessage());
        }
    }

    /**
     * Publish views
     */
    protected function publishViews(): void
    {
        try {
            Artisan::call('vendor:publish', [
                '--tag' => 'email-monitor-views',
                '--force' => $this->option('force')
            ]);
            $this->line('   ✓ Views published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish views: ' . $e->getMessage());
        }
    }

    /**
     * Run migrations
     */
    protected function runMigrations(): void
    {
        try {
            // Check if migrations table exists
            if (!Schema::hasTable('migrations')) {
                $this->warn('   ⚠️  Migrations table not found, creating...');
                Artisan::call('migrate:install');
            }

            // Check if email_logs table already exists
            if (Schema::hasTable('email_logs')) {
                $this->line('   ✓ Email logs table already exists');
                return;
            }

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $this->line('   ✓ Migrations completed');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to run migrations: ' . $e->getMessage());
            $this->warn('   Please run: php artisan migrate manually');
            throw $e;
        }
    }

    /**
     * Setup environment variables
     */
    protected function setupEnvironmentVariables(): void
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            $this->warn('   .env file not found, skipping environment setup');
            return;
        }

        $envContent = file_get_contents($envPath);
        
        // Check if email monitor config already exists
        if (strpos($envContent, 'EMAIL_MONITOR') !== false) {
            $this->line('   ✓ Environment variables already configured');
            return;
        }

        // Add email monitor configuration
        $emailMonitorConfig = "\n# Email Monitor Configuration\n";
        $emailMonitorConfig .= "EMAIL_MONITOR_ENABLED=true\n";
        $emailMonitorConfig .= "EMAIL_MONITOR_TIMEOUT_MINUTES=2\n";
        $emailMonitorConfig .= "EMAIL_MONITOR_NOTIFICATIONS_ENABLED=false\n";
        $emailMonitorConfig .= "EMAIL_MONITOR_NOTIFICATION_EMAILS=\n";

        file_put_contents($envPath, $envContent . $emailMonitorConfig);
        $this->line('   ✓ Environment variables added');
    }

    /**
     * Setup routes
     */
    protected function setupRoutes(): void
    {
        // No longer automatically adds comment to routes/web.php. This is now only handled by Install.php install command.
    }

    /**
     * Verify installation
     */
    protected function verifyInstallation(): void
    {
        $checks = [
            'Configuration file' => file_exists(config_path('email-monitor.php')),
            'Migration file' => file_exists(database_path('migrations/2024_01_01_000000_create_email_logs_table.php')),
            'Views published' => is_dir(resource_path('views/vendor/email-monitor')),
            'Database table' => Schema::hasTable('email_logs'),
            'Routes loaded' => $this->checkRoutesLoaded(),
        ];

        $allPassed = true;
        foreach ($checks as $check => $passed) {
            if ($passed) {
                $this->line("   ✓ {$check}");
            } else {
                $this->error("   ✗ {$check}");
                $allPassed = false;
            }
        }

        if (!$allPassed) {
            $this->warn('   ⚠️  Some checks failed. You may need to run setup again with --force');
        }
    }

    /**
     * Check if routes are loaded
     */
    protected function checkRoutesLoaded(): bool
    {
        try {
            $routes = Artisan::call('route:list', ['--name' => 'email-monitor']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
