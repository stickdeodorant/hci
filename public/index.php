<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Config;
use App\Core\Router;
use App\Core\View;
use App\Controllers\ThankYouController;

// Initialize configuration
$config = Config::getInstance();

// Set error reporting
if ($config->get('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set('America/New_York');

// Start session
session_start();

// Create router instance
$router = new Router();

// Define routes - Using inline handlers for now
$router->get('/', function($request, $response) {
    $config = Config::getInstance();
    
    $data = [
        'title' => 'Affordable Health Insurance Plans',
        'sitename' => $config->get('app.name'),
        'state' => $request->get('st') ? $config->get('states.' . strtoupper($request->get('st'))) : null,
        'state_abbr' => $request->get('st') ? strtoupper($request->get('st')) : null,
        'config' => $config
    ];
    
    echo View::render('home', $data);
});

$router->get('/get-quotes', function($request, $response) {
    $config = Config::getInstance();
    
    $data = [
        'title' => 'Get Free Health Insurance Quotes',
        'sitename' => $config->get('app.name'),
        'config' => $config,
        'zip' => $request->get('zip', '')
    ];
    
    echo View::render('multi-step-form', $data);
});

$router->get('/thank-you', [ThankYouController::class, 'index']);

// API routes
$router->post('/api/check-phone', function($request, $response) {
    $response->json(['success' => true]);
});

$router->post('/api/submit-lead', function($request, $response) {
    $response->json([
        'success' => true,
        'redirect' => '/thank-you'
    ]);
});

// Resolve the current route
try {
    $router->resolve();
} catch (Exception $e) {
    if ($config->get('app.debug')) {
        echo "<pre>Error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "</pre>";
    } else {
        echo "An error occurred. Please try again later.";
    }
}