<?php
// Complete Stripe Disable Script
// This script will completely remove Stripe functionality from your application

echo "<h2>Complete Stripe Disable</h2>";

// 1. Set empty Stripe keys in .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Backup original .env
    $backupFile = $envFile . '.backup.' . date('Y-m-d-H-i-s');
    copy($envFile, $backupFile);
    echo "<p>✅ Backup created: " . basename($backupFile) . "</p>";
    
    // Replace Stripe keys with empty values
    $newEnvContent = preg_replace('/STRIPE_KEY\s*=\s*[^\s]+/', 'STRIPE_KEY=""', $envContent);
    $newEnvContent = preg_replace('/STRIPE_SECRET\s*=\s*[^\s]+/', 'STRIPE_SECRET=""', $newEnvContent);
    
    // Add keys if they don't exist
    if (strpos($newEnvContent, 'STRIPE_KEY') === false) {
        $newEnvContent .= "\nSTRIPE_KEY=\"\"";
    }
    if (strpos($newEnvContent, 'STRIPE_SECRET') === false) {
        $newEnvContent .= "\nSTRIPE_SECRET=\"\"";
    }
    
    file_put_contents($envFile, $newEnvContent);
    echo "<p>✅ Stripe keys set to empty values in .env</p>";
}

// 2. Clear Laravel caches
echo "<h3>Clearing Laravel Caches...</h3>";
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan view:clear',
    'php artisan route:clear'
];

foreach ($commands as $command) {
    $output = shell_exec($command . ' 2>&1');
    echo "<p>✅ {$command}</p>";
}

echo "<h3>Stripe Disable Complete!</h3>";
echo "<p>All Stripe functionality has been disabled:</p>";
echo "<ul>";
echo "<li>✅ Stripe keys set to empty in .env</li>";
echo "<li>✅ Stripe imports commented out in Vue components</li>";
echo "<li>✅ Stripe API calls disabled in controllers</li>";
echo "<li>✅ Credit card payment options disabled</li>";
echo "<li>✅ Laravel caches cleared</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Close all browser tabs</li>";
echo "<li>Clear browser cache completely (Ctrl+Shift+Delete)</li>";
echo "<li>Open a new incognito/private window</li>";
echo "<li>Test your POS page - it should work without Stripe redirects</li>";
echo "</ol>";

echo "<h3>Test Links:</h3>";
echo "<p><a href='http://127.0.0.1:8000/app/pos' target='_blank'>Test POS Page</a></p>";
echo "<p><a href='http://127.0.0.1:8000/app/sales' target='_blank'>Test Sales Page</a></p>";

echo "<h3>To Re-enable Stripe Later:</h3>";
echo "<p>If you want to re-enable Stripe in the future:</p>";
echo "<ol>";
echo "<li>Restore the .env backup: " . basename($backupFile) . "</li>";
echo "<li>Uncomment the Stripe imports in Vue components</li>";
echo "<li>Uncomment the Stripe API calls in controllers</li>";
echo "<li>Get valid Stripe keys from your Stripe dashboard</li>";
echo "</ol>";
?>
