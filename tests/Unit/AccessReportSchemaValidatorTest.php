<?php

namespace Tests\Unit;

use App\Services\AccessValidator\AccessReportSchemaValidator;
use PHPUnit\Framework\TestCase;

class AccessReportSchemaValidatorTest extends TestCase
{
    public function test_valid_report_shape_is_accepted(): void
    {
        $validator = new AccessReportSchemaValidator();

        $report = [
            'schema_version' => 'access-validator.v1',
            'generated_at' => '2026-09-11T00:00:00+00:00',
            'source' => [
                'provider' => '20i',
                'connection_reference' => 'source-conn-1',
                'capabilities' => [
                    'package_listing' => [
                        'status' => 'pass',
                    ],
                ],
            ],
            'destination' => [
                'provider' => 'cpanel',
                'connection_reference' => 'dest-conn-1',
                'capabilities' => [
                    'applist' => [
                        'status' => 'pass',
                    ],
                ],
            ],
            'migration_possible' => true,
            'blocking_reasons' => [],
            'manual_actions' => [],
            'warnings' => [],
        ];

        $this->assertTrue($validator->validate($report));
        $this->assertSame([], $validator->errors());
    }

    public function test_invalid_report_shape_is_rejected(): void
    {
        $validator = new AccessReportSchemaValidator();

        $report = [
            'schema_version' => 'access-validator.v1',
            'generated_at' => 'not-a-date-time',
            'source' => [
                'provider' => '20i',
                'capabilities' => [],
            ],
            'destination' => [
                'provider' => 'cpanel',
                'capabilities' => [
                    'applist' => [
                        'status' => 'pass',
                    ],
                ],
            ],
            'migration_possible' => 'yes',
            'blocking_reasons' => [],
            'manual_actions' => [],
        ];

        $this->assertFalse($validator->validate($report));
        $this->assertNotSame([], $validator->errors());
    }
}
