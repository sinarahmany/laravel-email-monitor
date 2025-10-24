<?php

namespace Laravel\EmailMonitor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class AutoSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-monitor:setup 
                            {--force : Force setup even if already configured}
                            {--migrate : Run migrations automatically}';

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

        // Step 4: Run migrations
        if ($this->option('migrate')) {
            $this->info('⚡ Running migrations...');
            $this->runMigrations();
        }

        // Step 5: Create sample environment variables
        $this->info('🔧 Setting up environment variables...');
        $this->setupEnvironmentVariables();

        // Step 6: Create sample routes
        $this->info('🛣️ Setting up routes...');
        $this->setupRoutes();

        $this->newLine();
        $this->info('✅ Email Monitor setup complete!');
        $this->newLine();
        
        $this->info('Next steps:');
        $this->line('1. Run: php artisan migrate');
        $this->line('2. Visit: /email-monitor to access the dashboard');
        $this->line('3. Configure your email settings in .env file');
        $this->line('4. Start sending emails to see them monitored!');
        
        $this->newLine();
        $this->info('📚 Documentation: https://github.com/your-repo/email-monitor');
        
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
            Artisan::call('migrate', ['--force' => true]);
            $this->line('   ✓ Migrations completed');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to run migrations: ' . $e->getMessage());
            $this->warn('   Please run: php artisan migrate');
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

        // Add email monitor routes
        $emailMonitorRoutes = "\n// Email Monitor Routes (automatically added)\n";
        $emailMonitorRoutes .= "Route::get('/email-monitor', function () {\n";
        $emailMonitorRoutes .= "    return redirect()->route('email-monitor.index');\n";
        $emailMonitorRoutes .= "});\n";

        file_put_contents($routesPath, $routesContent . $emailMonitorRoutes);
        $this->line('   ✓ Routes added');
    }
}
