<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestSyncAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:test-api {--base-url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Sync API endpoints';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $baseUrl = $this->option('base-url') ?: 'http://localhost:8000/api';
        
        $this->info("Testing Sync API endpoints with base URL: {$baseUrl}");
        $this->newLine();

        // Test 1: Get available tables
        $this->info('1. Testing GET /sync/tables');
        try {
            $response = Http::get("{$baseUrl}/sync/tables");
            if ($response->successful()) {
                $data = $response->json();
                $this->info("✓ Success! Found " . count($data['data']) . " tables");
                foreach ($data['data'] as $table) {
                    $this->line("  - {$table['table']} ({$table['model_class']})");
                }
            } else {
                $this->error("✗ Failed! Status: " . $response->status());
                $this->error("Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception: " . $e->getMessage());
        }
        $this->newLine();

        // Test 2: Get sync status
        $this->info('2. Testing GET /sync/status');
        try {
            $response = Http::get("{$baseUrl}/sync/status");
            if ($response->successful()) {
                $data = $response->json();
                $this->info("✓ Success! Status retrieved for " . count($data['data']) . " tables");
            } else {
                $this->error("✗ Failed! Status: " . $response->status());
                $this->error("Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception: " . $e->getMessage());
        }
        $this->newLine();

        // Test 3: Test pull for a specific table (if tables exist)
        $this->info('3. Testing GET /sync/pull/{table}');
        try {
            $response = Http::get("{$baseUrl}/sync/tables");
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data'])) {
                    $firstTable = $data['data'][0]['table'];
                    $this->info("Testing pull for table: {$firstTable}");
                    
                    $pullResponse = Http::get("{$baseUrl}/sync/pull/{$firstTable}");
                    if ($pullResponse->successful()) {
                        $pullData = $pullResponse->json();
                        $this->info("✓ Success! Pulled " . count($pullData) . " records from {$firstTable}");
                    } else {
                        $this->error("✗ Pull failed! Status: " . $pullResponse->status());
                        $this->error("Response: " . $pullResponse->body());
                    }
                } else {
                    $this->warn("No tables available for testing pull");
                }
            } else {
                $this->error("✗ Could not get tables for pull test");
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception: " . $e->getMessage());
        }
        $this->newLine();

        // Test 4: Test push for a specific table
        $this->info('4. Testing POST /sync/push/{table}');
        try {
            $response = Http::get("{$baseUrl}/sync/tables");
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data'])) {
                    $firstTable = $data['data'][0]['table'];
                    $this->info("Testing push for table: {$firstTable}");
                    
                    // Test with empty array
                    $pushResponse = Http::post("{$baseUrl}/sync/push/{$firstTable}", []);
                    if ($pushResponse->successful()) {
                        $pushData = $pushResponse->json();
                        $this->info("✓ Success! Push response: " . $pushData['message']);
                    } else {
                        $this->error("✗ Push failed! Status: " . $pushResponse->status());
                        $this->error("Response: " . $pushResponse->body());
                    }
                } else {
                    $this->warn("No tables available for testing push");
                }
            } else {
                $this->error("✗ Could not get tables for push test");
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception: " . $e->getMessage());
        }
        $this->newLine();

        // Test 5: Test full sync
        $this->info('5. Testing POST /sync/full');
        try {
            $response = Http::post("{$baseUrl}/sync/full");
            if ($response->successful()) {
                $data = $response->json();
                $this->info("✓ Success! Full sync completed");
                $this->info("Message: " . $data['message']);
            } else {
                $this->error("✗ Failed! Status: " . $response->status());
                $this->error("Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception: " . $e->getMessage());
        }
        $this->newLine();

        $this->info('API testing completed!');
        return 0;
    }
}
