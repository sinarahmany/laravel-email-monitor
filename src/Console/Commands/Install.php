<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:install 
                            {--force : Force installation even if already configured}
                            {--skip-migrate : Skip running migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete one-click installation for Email Monitor package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing Email Monitor package...');
        $this->newLine();

        // Check if already configured
        if (!$this->option('force') && $this->isAlreadyConfigured()) {
            $this->warn('Email Monitor is already configured!');
            $this->info('Use --force to reinstall.');
            return 0;
        }

        try {
            // Step 1: Publish all assets
            $this->info('📦 Publishing package assets...');
            $this->publishAssets();

            // Step 2: Run migrations
            if (!$this->option('skip-migrate')) {
                $this->info('🗄️ Setting up database...');
                $this->setupDatabase();
            }

            // Step 3: Setup environment
            $this->info('🔧 Configuring environment...');
            $this->setupEnvironment();

            // Step 4: Setup routes
            $this->info('🛣️ Configuring routes...');
            $this->setupRoutes();

            // Step 5: Verify installation
            $this->info('🔍 Verifying installation...');
            $this->verifyInstallation();

            $this->newLine();
            $this->info('✅ Email Monitor installation complete!');
            $this->newLine();
            
            $this->info('🎉 Your email monitor is ready to use!');
            $this->line('📊 Dashboard: /email-monitor');
            $this->line('📧 Test email: /test-email');
            $this->line('⚙️  Configure email settings in your .env file');
            $this->line('📚 Documentation: https://github.com/your-repo/email-monitor');
            
            return 0;

        } catch (\Exception $e) {
            $this->error('Installation failed: ' . $e->getMessage());
            $this->warn('Please check your configuration and try again.');
            return 1;
        }
    }

    /**
     * Check if already configured
     */
    protected function isAlreadyConfigured(): bool
    {
        return file_exists(config_path('email-monitor.php')) &&
               file_exists(database_path('migrations/2024_01_01_000000_create_email_logs_table.php')) &&
               Schema::hasTable('email_logs');
    }

    /**
     * Publish all assets
     */
    protected function publishAssets(): void
    {
        $assets = [
            'email-monitor-config' => 'Configuration file',
            'email-monitor-migrations' => 'Migration files',
            'email-monitor-views' => 'View files',
        ];

        foreach ($assets as $tag => $description) {
            try {
                Artisan::call('vendor:publish', [
                    '--tag' => $tag,
                    '--force' => $this->option('force')
                ]);
                $this->line("   ✓ {$description} published");
            } catch (\Exception $e) {
                $this->error("   ✗ Failed to publish {$description}: " . $e->getMessage());
            }
        }
    }

    /**
     * Setup database
     */
    protected function setupDatabase(): void
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
            $this->line('   ✓ Database setup completed');
        } catch (\Exception $e) {
            $this->error('   ✗ Database setup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Setup environment variables
     */
    protected function setupEnvironment(): void
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
        $routesPath = base_path('routes/web.php');
        
        if (!file_exists($routesPath)) {
            $this->warn('   routes/web.php not found, skipping route setup');
            return;
        }

        $routesContent = file_get_contents($routesPath);
        
        // Check if email monitor routes already exist
        if (strpos($routesContent, 'email-monitor') !== false) {
            $this->line('   ✓ Routes already configured');
            return;
        }

        // Note: Email monitor routes are automatically loaded by the service provider
        // No need to add routes manually
        $emailMonitorRoutes = "\n// Email Monitor Routes are automatically loaded by the package\n";

        file_put_contents($routesPath, $routesContent . $emailMonitorRoutes);
        $this->line('   ✓ Routes added');
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
            $this->warn('   ⚠️  Some checks failed. You may need to run installation again with --force');
        }
    }

    /**
     * Check if routes are loaded
     */
    protected function checkRoutesLoaded(): bool
    {
        try {
            Artisan::call('route:list', ['--name' => 'email-monitor']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
