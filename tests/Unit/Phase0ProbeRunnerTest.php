<?php

namespace Tests\Unit;

use App\Services\AccessValidator\Phase0ProbeRunner;
use PHPUnit\Framework\TestCase;

class Phase0ProbeRunnerTest extends TestCase
{
    public function test_probe_runner_builds_schema_compliant_report_shape(): void
    {
        $runner = new Phase0ProbeRunner();

        $report = $runner->runReadOnlyProbe(
            [
                'package_listing' => ['status' => 'pass'],
            ],
            [
                'applist' => ['status' => 'pass'],
            ]
        );

        $this->assertSame('access-validator.v1', $report['schema_version']);
        $this->assertSame('20i', $report['source']['provider']);
        $this->assertSame('cpanel', $report['destination']['provider']);
        $this->assertArrayHasKey('capabilities', $report['source']);
        $this->assertArrayHasKey('capabilities', $report['destination']);
    }
}
