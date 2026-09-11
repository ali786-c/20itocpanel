<?php

use App\Services\AccessValidator\AccessReportSchemaValidator;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('access:validate-report {path}', function (string $path) {
    $validator = new AccessReportSchemaValidator();

    if (!is_file($path)) {
        $this->error("Report file not found: {$path}");
        return 1;
    }

    $report = json_decode((string) file_get_contents($path), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $this->error('Invalid JSON input.');
        return 1;
    }

    if ($validator->validate($report)) {
        $this->info('Report schema validation passed.');
        return 0;
    }

    foreach ($validator->errors() as $error) {
        $this->error($error);
    }

    return 1;
})->purpose('Validate the Phase 0 access-validator report JSON.');
