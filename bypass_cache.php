<?php
// Browser cache bypass script
// This adds cache-busting headers to prevent Stripe redirect caching

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

echo "<h2>Cache Bypass Complete</h2>";
echo "<p>✅ Cache headers set to prevent Stripe redirect caching</p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Close all browser tabs for your application</li>";
echo "<li>Clear browser cache completely (Ctrl+Shift+Delete)</li>";
echo "<li>Open a new incognito/private window</li>";
echo "<li>Navigate to your POS page</li>";
echo "</ol>";

echo "<h3>If Still Redirecting:</h3>";
echo "<ul>";
echo "<li>Try a different browser</li>";
echo "<li>Disable browser extensions</li>";
echo "<li>Check if your DNS is cached (flush DNS)</li>";
echo "<li>Restart your computer</li>";
echo "</ul>";

echo "<h3>Quick Test:</h3>";
echo "<p><a href='http://127.0.0.1:8000/app/pos' target='_blank'>Test POS Page (New Tab)</a></p>";
?>
