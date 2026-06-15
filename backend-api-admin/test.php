<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::whereHas('schoolMemberships')->first();
$user->load('schoolMemberships.school');

$resource = new \App\Http\Resources\Api\V1\Auth\UserResource($user);
echo json_encode($resource->resolve()['school_memberships']);
