<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/v1/schools/16/classes', 'GET');
// Let's pretend we're logged in as user with ID = the headmaster of school 16
$user = \App\Models\SchoolMember::where('school_id', 16)->where('role_type', 'headmaster')->first()->user;
$request->setUserResolver(function () use ($user) {
    return $user;
});
$response = app()->handle($request);
echo $response->getContent();
