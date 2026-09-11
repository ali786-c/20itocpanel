<?php

namespace App\Services\AccessValidator;

class Phase0ProbeRunner
{
    public function buildReport(array $source, array $destination): array
    {
        return [
            'schema_version' => 'access-validator.v1',
            'generated_at' => gmdate('c'),
            'source' => [
                'provider' => '20i',
                'connection_reference' => $source['connection_reference'] ?? null,
                'capabilities' => $source['capabilities'] ?? [
                    'package_listing' => ['status' => 'not_tested'],
                    'domain_root_inventory' => ['status' => 'not_tested'],
                ],
            ],
            'destination' => [
                'provider' => 'cpanel',
                'connection_reference' => $destination['connection_reference'] ?? null,
                'capabilities' => $destination['capabilities'] ?? [
                    'applist' => ['status' => 'not_tested'],
                    'uapi_cpanel' => ['status' => 'not_tested'],
                ],
            ],
            'migration_possible' => false,
            'blocking_reasons' => [],
            'manual_actions' => [],
            'warnings' => [],
        ];
    }

    public function runReadOnlyProbe(array $sourceCapabilities, array $destinationCapabilities): array
    {
        $source = [
            'provider' => '20i',
            'connection_reference' => 'source-conn-placeholder',
            'capabilities' => $sourceCapabilities,
        ];

        $destination = [
            'provider' => 'cpanel',
            'connection_reference' => 'dest-conn-placeholder',
            'capabilities' => $destinationCapabilities,
        ];

        return $this->buildReport($source, $destination);
    }
}
