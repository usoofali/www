<?php
// Quick fix to disable Stripe redirect issue
// This script will help you temporarily disable Stripe functionality

echo "<h2>Stripe Redirect Fix</h2>";

// Option 1: Set empty Stripe keys in .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Check current values
    $hasStripeKey = strpos($envContent, 'STRIPE_KEY') !== false;
    $hasStripeSecret = strpos($envContent, 'STRIPE_SECRET') !== false;
    
    echo "<p>Current .env status:</p>";
    echo "<ul>";
    echo "<li>STRIPE_KEY: " . ($hasStripeKey ? 'Found' : 'Not found') . "</li>";
    echo "<li>STRIPE_SECRET: " . ($hasStripeSecret ? 'Found' : 'Not found') . "</li>";
    echo "</ul>";
    
    // Option to disable Stripe
    if (isset($_GET['disable_stripe']) && $_GET['disable_stripe'] === '1') {
        // Backup original .env
        copy($envFile, $envFile . '.backup.' . date('Y-m-d-H-i-s'));
        
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
        echo "<p>✅ Stripe has been disabled. Keys set to empty values.</p>";
        echo "<p>Backup created: .env.backup." . date('Y-m-d-H-i-s') . "</p>";
    } else {
        echo "<p><a href='?disable_stripe=1' style='background: #dc3545; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Disable Stripe (Set Empty Keys)</a></p>";
    }
    
    // Option to restore from backup
    $backups = glob($envFile . '.backup.*');
    if (!empty($backups)) {
        echo "<h3>Available Backups:</h3>";
        foreach ($backups as $backup) {
            $backupDate = str_replace($envFile . '.backup.', '', $backup);
            echo "<p><a href='?restore=" . urlencode($backup) . "'>Restore from " . $backupDate . "</a></p>";
        }
    }
    
    if (isset($_GET['restore'])) {
        $backupFile = $_GET['restore'];
        if (file_exists($backupFile)) {
            copy($backupFile, $envFile);
            echo "<p>✅ .env restored from backup.</p>";
        }
    }
    
} else {
    echo "<p>❌ .env file not found</p>";
}

echo "<h3>Manual Fix Options:</h3>";
echo "<ol>";
echo "<li><strong>Quick Fix:</strong> Set STRIPE_KEY=\"\" in your .env file</li>";
echo "<li><strong>Proper Fix:</strong> Get valid Stripe keys from your Stripe dashboard</li>";
echo "<li><strong>Disable Stripe:</strong> Remove credit card payment option from POS</li>";
echo "</ol>";

echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>Clear your browser cache</li>";
echo "<li>Restart your Laravel application</li>";
echo "<li>Test the POS page again</li>";
echo "</ul>";
?>
