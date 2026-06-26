<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/v1/schools/transfer/accept/fWUM0974awLeBpsw73BLTt776sLoid5P318CwUAZ4KzEWMfpbf0h6Jp2X0Hr4Cia', 'GET');
// For a test, we might need a fake authenticated user to pass auth:sanctum
// Let's just test without auth to see if it hits the controller or a 404
$response = $kernel->handle($request);
echo 'Status: ' . $response->getStatusCode() . PHP_EOL;
echo 'Content: ' . $response->getContent() . PHP_EOL;
