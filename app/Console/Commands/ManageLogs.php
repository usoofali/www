<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ManageLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:manage 
                            {action : The action to perform (status|disable-sync|enable-sync|disable-debug|enable-debug|clear|optimize|rotate)}
                            {--force : Force the action without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Laravel logging configuration and log files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $force = $this->option('force');

        switch ($action) {
            case 'status':
                return $this->showStatus();
                
            case 'disable-sync':
                return $this->disableSyncLogging($force);
                
            case 'enable-sync':
                return $this->enableSyncLogging($force);
                
            case 'disable-debug':
                return $this->disableDebugLogging($force);
                
            case 'enable-debug':
                return $this->enableDebugLogging($force);
                
            case 'clear':
                return $this->clearLogs($force);
                
            case 'optimize':
                return $this->optimizeLogging($force);
                
            case 'rotate':
                return $this->rotateLogs($force);
                
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: status, disable-sync, enable-sync, disable-debug, enable-debug, clear, optimize, rotate");
                return 1;
        }
    }

    /**
     * Show current logging status
     */
    protected function showStatus()
    {
        $this->info('📊 Current Logging Status');
        $this->line('');

        // Show configuration
        $this->info('Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['LOG_CHANNEL', config('logging.default')],
                ['LOG_LEVEL', config('logging.channels.single.level')],
                ['SYNC_LOGGING_ENABLED', config('app.sync_logging_enabled') ? 'true' : 'false'],
                ['DEBUG_LOGGING_ENABLED', config('app.debug_logging_enabled') ? 'true' : 'false'],
            ]
        );

        // Show log file sizes
        $this->info('Log File Sizes:');
        $logFiles = [
            'laravel.log' => storage_path('logs/laravel.log'),
            'sync.log' => storage_path('logs/sync.log'),
            'debug.log' => storage_path('logs/debug.log'),
            'error.log' => storage_path('logs/error.log')
        ];

        $fileData = [];
        foreach ($logFiles as $name => $path) {
            if (file_exists($path)) {
                $size = filesize($path);
                $sizeMB = round($size / 1024 / 1024, 2);
                $fileData[] = [$name, $sizeMB . ' MB'];
            } else {
                $fileData[] = [$name, 'Not found'];
            }
        }

        $this->table(['File', 'Size'], $fileData);

        return 0;
    }

    /**
     * Disable sync logging
     */
    protected function disableSyncLogging($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to disable sync logging?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->updateEnvFile('SYNC_LOGGING_ENABLED', 'false');
        $this->info('✅ Sync logging disabled');
        
        $this->clearCache();
        return 0;
    }

    /**
     * Enable sync logging
     */
    protected function enableSyncLogging($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to enable sync logging?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->updateEnvFile('SYNC_LOGGING_ENABLED', 'true');
        $this->info('✅ Sync logging enabled');
        
        $this->clearCache();
        return 0;
    }

    /**
     * Disable debug logging
     */
    protected function disableDebugLogging($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to disable debug logging?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->updateEnvFile('DEBUG_LOGGING_ENABLED', 'false');
        $this->updateEnvFile('LOG_LEVEL', 'error');
        $this->info('✅ Debug logging disabled');
        
        $this->clearCache();
        return 0;
    }

    /**
     * Enable debug logging
     */
    protected function enableDebugLogging($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to enable debug logging?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->updateEnvFile('DEBUG_LOGGING_ENABLED', 'true');
        $this->updateEnvFile('LOG_LEVEL', 'debug');
        $this->info('✅ Debug logging enabled');
        
        $this->clearCache();
        return 0;
    }

    /**
     * Clear all log files
     */
    protected function clearLogs($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to clear all log files? This action cannot be undone.')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $logFiles = [
            'laravel.log' => storage_path('logs/laravel.log'),
            'sync.log' => storage_path('logs/sync.log'),
            'debug.log' => storage_path('logs/debug.log'),
            'error.log' => storage_path('logs/error.log')
        ];

        $cleared = 0;
        foreach ($logFiles as $name => $path) {
            if (file_exists($path)) {
                file_put_contents($path, '');
                $this->line("🗑️  Cleared {$name}");
                $cleared++;
            }
        }

        $this->info("✅ Cleared {$cleared} log files");
        return 0;
    }

    /**
     * Optimize logging for production
     */
    protected function optimizeLogging($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to optimize logging for production?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $optimizations = [
            'LOG_CHANNEL' => 'daily',
            'LOG_LEVEL' => 'error',
            'LOG_DAYS' => '7',
            'SYNC_LOGGING_ENABLED' => 'false',
            'DEBUG_LOGGING_ENABLED' => 'false',
            'ERROR_LOGGING_ENABLED' => 'true',
            'SYNC_LOG_LEVEL' => 'error',
            'SYNC_LOG_DAYS' => '7',
            'DEBUG_LOG_DAYS' => '3',
            'ERROR_LOG_DAYS' => '30'
        ];

        foreach ($optimizations as $key => $value) {
            $this->updateEnvFile($key, $value);
        }

        $this->info('✅ Logging optimized for production');
        $this->clearCache();
        return 0;
    }

    /**
     * Rotate log files
     */
    protected function rotateLogs($force)
    {
        if (!$force && !$this->confirm('Are you sure you want to rotate log files?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->call('queue:restart');
        $this->info('✅ Log files rotated');
        return 0;
    }

    /**
     * Update .env file
     */
    protected function updateEnvFile($key, $value)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            $this->error('❌ .env file not found');
            return false;
        }

        $content = file_get_contents($envFile);
        
        // Check if key exists
        if (strpos($content, $key . '=') !== false) {
            // Update existing key
            $content = preg_replace("/^{$key}\s*=\s*[^\s]+/m", "{$key}={$value}", $content);
        } else {
            // Add new key
            $content .= "\n{$key}={$value}";
        }
        
        file_put_contents($envFile, $content);
        return true;
    }

    /**
     * Clear Laravel cache
     */
    protected function clearCache()
    {
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->line('🧹 Cache cleared');
    }
}
