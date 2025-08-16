<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncService;

class TestSyncService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the SyncService to verify model discovery and database connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing SyncService...');
        
        try {
            $syncService = new SyncService();
            
            // Test model discovery
            $this->info('Testing model discovery...');
            $models = $syncService->discoverModels();
            
            if (empty($models)) {
                $this->error('No models discovered!');
                return 1;
            }
            
            $this->info('Discovered ' . count($models) . ' models:');
            foreach ($models as $tableName => $modelInfo) {
                $this->line("  - {$tableName} ({$modelInfo['class']})");
            }
            
            // Test sync status
            $this->info('Testing sync status...');
            $status = $syncService->getSyncStatus();
            
            $this->info('Sync status for each model:');
            foreach ($status as $tableName => $modelStatus) {
                $this->line("  - {$tableName}:");
                $this->line("    Last sync: {$modelStatus['last_sync']}");
                $this->line("    Total records: {$modelStatus['total_records']}");
                $this->line("    Pending sync: {$modelStatus['pending_sync']}");
            }
            
            $this->info('SyncService test completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('SyncService test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
