<?php
/**
 * Test Logout Endpoint
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get Auth Service
$auth = app('auth');

// Simulate a logged-in user
$user = \App\Models\User::first();

if (!$user) {
    echo "❌ Tidak ada user di database. Silakan buat user terlebih dahulu.\n";
    exit;
}

echo "=== TESTING LOGOUT FUNCTIONALITY ===\n\n";
echo "Test User: {$user->name} ({$user->email})\n";
echo "User ID: {$user->id}\n";
echo "User Role: {$user->role}\n\n";

// Create a fake POST request for logout
$request = Illuminate\Http\Request::create(
    '/logout',
    'POST',
    []
);
$request->setLaravelSession($app->make('session')->driver());

// Manually set up session for testing
$request->setUserResolver(function () use ($user) {
    return $user;
});

try {
    $controller = new \App\Http\Controllers\AuthController();
    
    // Manual login simulation
    auth()->login($user);
    echo "✓ User di-login secara manual\n";
    echo "✓ Auth check: " . (auth()->check() ? "LOGGED IN" : "NOT LOGGED IN") . "\n\n";
    
    // Test logout
    echo "Testing logout...\n";
    $response = $controller->logout($request);
    
    echo "✓ Response Type: " . get_class($response) . "\n";
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✓ Redirect Location: " . $response->getTargetUrl() . "\n";
        echo "✓ Logout berhasil!\n";
        
        // Check if user is still logged in
        echo "\n✓ Auth check setelah logout: " . (auth()->check() ? "STILL LOGGED IN (ERROR!)" : "LOGGED OUT (OK)") . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
