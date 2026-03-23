<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$enrollment = App\Models\CourseEnrollment::where('user_id', 32)
    ->whereNotNull('certificate_url')
    ->first();

if (!$enrollment) {
    echo "No enrollment found\n";
    exit(1);
}

echo "Found enrollment #{$enrollment->id}\n";
echo "Old path: {$enrollment->certificate_url}\n";

$gen = new App\Services\Lms\CertificateGeneratorService();
$newPath = $gen->generateForEnrollment($enrollment);

$enrollment->certificate_url = $newPath;
$enrollment->save();

echo "New path: {$newPath}\n";
echo "Done! Certificate regenerated.\n";
