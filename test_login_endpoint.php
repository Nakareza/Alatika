<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a fake POST request to login endpoint
$request = \Illuminate\Http\Request::create(
    '/login',
    'POST',
    [
        'nim' => '23010001',
        'password' => 'password',
    ]
);

echo "=== TESTING LOGIN ENDPOINT DIRECTLY ===\n\n";
echo "Request: POST /login\n";
echo "Data: NIM='23010001', Password='password'\n\n";

try {
    $controller = new \App\Http\Controllers\AuthController();
    $response = $controller->login($request);
    
    echo "Response Type: " . get_class($response) . "\n";
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "Redirect Location: " . $response->getTargetUrl() . "\n";
        echo "Status: " . $response->getStatusCode() . "\n";
    } elseif ($response instanceof \Illuminate\View\View) {
        echo "View Name: " . $response->getName() . "\n";
    }
    
    echo "\n✓ Login endpoint executed\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
