<?php
// Debug script to check Stripe configuration
// Place this in your public directory and access it via browser

echo "<h2>Stripe Configuration Debug</h2>";

// Check if .env file exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "<p>✅ .env file exists</p>";
    
    // Read .env file
    $envContent = file_get_contents($envFile);
    
    // Check for Stripe keys
    if (strpos($envContent, 'STRIPE_KEY') !== false) {
        echo "<p>✅ STRIPE_KEY found in .env</p>";
        
        // Extract Stripe key (basic extraction)
        preg_match('/STRIPE_KEY\s*=\s*([^\s]+)/', $envContent, $matches);
        if (isset($matches[1])) {
            $stripeKey = trim($matches[1], '"\'');
            echo "<p>Stripe Key: " . substr($stripeKey, 0, 10) . "...</p>";
            
            if (empty($stripeKey) || $stripeKey === 'null' || $stripeKey === '') {
                echo "<p>❌ STRIPE_KEY is empty or null</p>";
            } else {
                echo "<p>✅ STRIPE_KEY has a value</p>";
            }
        }
    } else {
        echo "<p>❌ STRIPE_KEY not found in .env</p>";
    }
    
    if (strpos($envContent, 'STRIPE_SECRET') !== false) {
        echo "<p>✅ STRIPE_SECRET found in .env</p>";
    } else {
        echo "<p>❌ STRIPE_SECRET not found in .env</p>";
    }
    
} else {
    echo "<p>❌ .env file not found</p>";
}

// Check Laravel config
echo "<h3>Laravel Configuration</h3>";
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $stripeKey = config('app.STRIPE_KEY');
    $stripeSecret = config('app.STRIPE_SECRET');
    
    echo "<p>Config STRIPE_KEY: " . (empty($stripeKey) ? 'NULL/EMPTY' : substr($stripeKey, 0, 10) . '...') . "</p>";
    echo "<p>Config STRIPE_SECRET: " . (empty($stripeSecret) ? 'NULL/EMPTY' : 'SET') . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error loading Laravel: " . $e->getMessage() . "</p>";
}

echo "<h3>Recommendations</h3>";
echo "<ul>";
echo "<li>If STRIPE_KEY is empty, set it in your .env file</li>";
echo "<li>If you don't need Stripe, disable it in the POS page</li>";
echo "<li>Check if your Stripe account is active and the key is valid</li>";
echo "</ul>";
?>
