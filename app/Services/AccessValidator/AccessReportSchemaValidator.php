<?php

namespace App\Services\AccessValidator;

class AccessReportSchemaValidator
{
    private array $errors = [];

    public function validate(array $report): bool
    {
        $this->errors = [];

        if (($report['schema_version'] ?? null) !== 'access-validator.v1') {
            $this->errors[] = 'schema_version must be access-validator.v1';
        }

        if (!isset($report['generated_at']) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+\-]\d{2}:\d{2}$/', $report['generated_at'])) {
            $this->errors[] = 'generated_at must be an ISO-8601 date-time string';
        }

        if (!isset($report['source'], $report['source']['provider'], $report['source']['capabilities'])) {
            $this->errors[] = 'source provider and capabilities are required';
        }

        if (!isset($report['destination'], $report['destination']['provider'], $report['destination']['capabilities'])) {
            $this->errors[] = 'destination provider and capabilities are required';
        }

        if (!is_bool($report['migration_possible'] ?? null)) {
            $this->errors[] = 'migration_possible must be boolean';
        }

        if (!is_array($report['blocking_reasons'] ?? null)) {
            $this->errors[] = 'blocking_reasons must be array';
        }

        if (!is_array($report['manual_actions'] ?? null)) {
            $this->errors[] = 'manual_actions must be array';
        }

        if (isset($report['warnings']) && !is_array($report['warnings'])) {
            $this->errors[] = 'warnings must be array';
        }

        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
