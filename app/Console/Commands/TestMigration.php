<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Models\Credential;
use App\Models\SourceConnection;
use App\Models\DestinationConnection;
use App\Models\MigrationJob;
use App\Services\Migration\MigrationOrchestrator;
use App\Enums\JobStatus;

class TestMigration extends Command
{
    protected $signature = 'app:test-migration';
    protected $description = 'Creates a dummy migration job and runs it through the orchestrator';

    public function handle(MigrationOrchestrator $orchestrator)
    {
        $this->info('Seeding database with live data...');
        
        // Clean up previous test
        MigrationJob::truncate();
        DestinationConnection::truncate();
        SourceConnection::truncate();
        Credential::truncate();
        Organization::truncate();
        
        $org = Organization::create(['name' => 'Live Test Org']);
        
        // 20i requires base64 of the key according to plan (or plain token)
        $twentyIToken = 'c3ecb30255aed3b60'; // We can try raw, if it fails we can base64 it in the adapter. Let's send raw.
        
        $sourceCred = Credential::create([
            'organization_id' => $org->id,
            'name' => '20i Live Key',
            'type' => '20i_api_key',
            'encrypted_value' => base64_encode($twentyIToken) // Let's base64 it just in case as per docs
        ]);
        
        $destCred = Credential::create([
            'organization_id' => $org->id,
            'name' => 'cPanel Live Token',
            'type' => 'cpanel_uapi_token',
            'encrypted_value' => '1ZPCWS9Q6A55FOL1RAB5N0F2G52180DO'
        ]);
        
        $sourceConn = SourceConnection::create([
            'organization_id' => $org->id,
            'name' => '20i Live Source',
            'provider' => '20i',
            'credential_id' => $sourceCred->id
        ]);
        
        $destConn = DestinationConnection::create([
            'organization_id' => $org->id,
            'name' => 'cPanel Live Dest',
            'provider' => 'cpanel',
            'credential_id' => $destCred->id,
            'hostname' => 'https://client.webhosting.co.nz:2087'
        ]);
        
        $job = MigrationJob::create([
            'organization_id' => $org->id,
            'source_connection_id' => $sourceConn->id,
            'destination_connection_id' => $destConn->id,
            'status' => JobStatus::DRAFT->value
        ]);
        
        $this->info("Created Migration Job ID: {$job->id}");
        
        $this->info('Processing job via orchestrator (DRAFT state)...');
        
        $orchestrator->process($job);
        
        $job->refresh();
        $this->info("New Status: {$job->status}");
    }
}
