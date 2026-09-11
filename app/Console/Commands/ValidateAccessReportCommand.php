<?php

namespace App\Console\Commands;

use App\Services\AccessValidator\AccessReportSchemaValidator;
use Illuminate\Console\Command;

class ValidateAccessReportCommand extends Command
{
    protected $signature = 'access:validate-report {path}';

    protected $description = 'Validate a Phase 0 access-validator JSON report shape using the repository schema rules.';

    public function handle(AccessReportSchemaValidator $validator): int
    {
        $path = $this->argument('path');

        if (!is_file($path)) {
            $this->error("Report file not found: {$path}");
            return self::FAILURE;
        }

        $json = json_decode((string) file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON input.');
            return self::FAILURE;
        }

        if ($validator->validate($json)) {
            $this->info('Report schema validation passed.');
            return self::SUCCESS;
        }

        foreach ($validator->errors() as $error) {
            $this->error($error);
        }

        return self::FAILURE;
    }
}
