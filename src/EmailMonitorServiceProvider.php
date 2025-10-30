<?php

namespace Laravel\EmailMonitor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Events\MessageFailed;
use Laravel\EmailMonitor\Listeners\EmailSendingListener;
use Laravel\EmailMonitor\Listeners\EmailSentListener;
use Laravel\EmailMonitor\Listeners\EmailFailedListener;

class EmailMonitorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/email-monitor.php', 'email-monitor'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Auto-setup: Create migration if it doesn't exist
        $this->autoSetupMigration();
        
        // Auto-setup: Create config if it doesn't exist
        $this->autoSetupConfig();

        // Auto-setup: Ensure routes are loaded
        // $this->autoSetupRoutes();

        // Publish configuration file (optional)
        $this->publishes([
            __DIR__.'/../config/email-monitor.php' => config_path('email-monitor.php'),
        ], 'email-monitor-config');

        // Publish migrations (optional)
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'email-monitor-migrations');

        // Publish views (optional)
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/email-monitor'),
        ], 'email-monitor-views');

        // Load package views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'email-monitor');

        // Load package routes with proper middleware
        if (!$this->app->routesAreCached()) {
            require __DIR__.'/../routes/web.php';
        }

        // Register event listeners
        Event::listen(MessageSending::class, EmailSendingListener::class);
        Event::listen(MessageSent::class, EmailSentListener::class);
        Event::listen(MessageFailed::class, EmailFailedListener::class);

        // Register middleware
        $this->app['router']->aliasMiddleware('email-monitor', \Laravel\EmailMonitor\Middleware\EmailMonitorMiddleware::class);

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Laravel\EmailMonitor\Console\Commands\CleanupEmailLogs::class,
                \Laravel\EmailMonitor\Console\Commands\EmailMonitorStats::class,
                \Laravel\EmailMonitor\Console\Commands\EmailMonitorCleanup::class,
                \Laravel\EmailMonitor\Console\Commands\CheckStuckEmails::class,
                \Laravel\EmailMonitor\Console\Commands\AutoSetup::class,
                \Laravel\EmailMonitor\Console\Commands\Install::class,
            ]);
        }
    }

    /**
     * Auto-setup migration
     */
    protected function autoSetupMigration(): void
    {
        if (!config('email-monitor.auto_setup.create_migration', true)) {
            return;
        }

        $migrationPath = database_path('migrations');
        $migrationFile = $migrationPath . '/2024_01_01_000000_create_email_logs_table.php';
        
        if (!file_exists($migrationFile)) {
            if (!is_dir($migrationPath)) {
                mkdir($migrationPath, 0755, true);
            }
            
            $migrationContent = file_get_contents(__DIR__.'/../database/migrations/2024_01_01_000000_create_email_logs_table.php');
            file_put_contents($migrationFile, $migrationContent);
        }

        // Auto-run migrations if configured
        if (config('email-monitor.auto_setup.run_migrations', true) && !$this->app->runningInConsole()) {
            try {
                if (!Schema::hasTable('email_logs')) {
                    Artisan::call('migrate', ['--force' => true]);
                }
            } catch (\Exception $e) {
                // Silently fail in auto-setup to avoid breaking the app
            }
        }
    }

    /**
     * Auto-setup config
     */
    protected function autoSetupConfig(): void
    {
        if (!config('email-monitor.auto_setup.create_config', true)) {
            return;
        }

        $configPath = config_path('email-monitor.php');
        
        if (!file_exists($configPath)) {
            $configContent = file_get_contents(__DIR__.'/../config/email-monitor.php');
            file_put_contents($configPath, $configContent);
        }
    }

    /**
     * Auto-setup routes
     */
    protected function autoSetupRoutes(): void
    {
        if (!config('email-monitor.auto_setup.create_routes', true)) {
            return;
        }

        $routesPath = base_path('routes/web.php');
        
        if (!file_exists($routesPath)) {
            return;
        }

        $routesContent = file_get_contents($routesPath);
        
        // Check if email monitor routes already exist
        if (
            strpos($routesContent, 'email-monitor') !== false ||
            strpos($routesContent, 'Email Monitor Routes are automatically loaded by the package') !== false
        ) {
            return;
        }

        // Note: Email monitor routes are automatically loaded by the package
        // No need to add routes manually
        $emailMonitorRoutes = "\n// Email Monitor Routes are automatically loaded by the package\n";

        file_put_contents($routesPath, $routesContent . $emailMonitorRoutes);
    }
}
