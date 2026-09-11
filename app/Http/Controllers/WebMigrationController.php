<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MigrationJob;
use App\Models\Organization;
use App\Models\Credential;
use App\Models\SourceConnection;
use App\Models\DestinationConnection;
use App\Enums\JobStatus;
use App\Services\Migration\MigrationOrchestrator;
use Illuminate\Support\Facades\DB;

class WebMigrationController extends Controller
{
    public function index()
    {
        $jobs = MigrationJob::with(['organization', 'sourceConnection', 'destinationConnection'])
                ->orderBy('created_at', 'desc')
                ->get();
                
        return view('dashboard', compact('jobs'));
    }
    
    public function create()
    {
        return view('migrations.create');
    }
    
    public function store(Request $request, MigrationOrchestrator $orchestrator)
    {
        $request->validate([
            'org_name' => 'required|string|max:255',
            'token_20i' => 'required|string',
            'whm_token' => 'required|string',
            'whm_host' => 'required|url',
        ]);
        
        DB::beginTransaction();
        try {
            $org = Organization::create(['name' => $request->org_name]);
            
            $sourceCred = Credential::create([
                'organization_id' => $org->id,
                'name' => '20i Key',
                'type' => '20i_api_key',
                'encrypted_value' => base64_encode($request->input('token_20i'))
            ]);
            
            $destCred = Credential::create([
                'organization_id' => $org->id,
                'name' => 'WHM Token',
                'type' => 'whm_token',
                'encrypted_value' => $request->input('whm_token')
            ]);
            
            $sourceConn = SourceConnection::create([
                'organization_id' => $org->id,
                'name' => '20i Source',
                'provider' => '20i',
                'credential_id' => $sourceCred->id
            ]);
            
            $destConn = DestinationConnection::create([
                'organization_id' => $org->id,
                'name' => 'WHM Dest',
                'provider' => 'cpanel',
                'credential_id' => $destCred->id,
                'hostname' => $request->whm_host
            ]);
            
            $job = MigrationJob::create([
                'organization_id' => $org->id,
                'source_connection_id' => $sourceConn->id,
                'destination_connection_id' => $destConn->id,
                'status' => JobStatus::DRAFT->value
            ]);
            
            DB::commit();
            
            try {
                $orchestrator->process($job);
            } catch (\Exception $e) {
                // Ignore exception here so the UI doesn't crash on connection fail
            }
            
            return redirect()->route('dashboard')->with('success', 'Migration job created and processing started.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create job: ' . $e->getMessage());
        }
    }

    public function showMapping(string $id)
    {
        $job = MigrationJob::findOrFail($id);
        
        if ($job->status !== JobStatus::AWAITING_MAPPING->value) {
            return redirect()->route('dashboard')->with('error', 'This job is not awaiting mapping.');
        }

        return view('migrations.map', compact('job'));
    }

    public function submitMapping(Request $request, string $id, MigrationOrchestrator $orchestrator)
    {
        $job = MigrationJob::findOrFail($id);
        
        if ($job->status !== JobStatus::AWAITING_MAPPING->value) {
            return redirect()->route('dashboard')->with('error', 'This job is not awaiting mapping.');
        }

        $request->validate([
            'selected_packages' => 'required|array',
            'selected_packages.*' => 'string' // package names/ids
        ]);

        $selectedNames = $request->input('selected_packages', []);
        $currentManifest = $job->source_manifest ?? [];
        $newManifest = [];

        foreach ($currentManifest as $pkg) {
            $identifier = $pkg['id'] ?? $pkg['name'] ?? json_encode($pkg);
            if (in_array((string)$identifier, $selectedNames)) {
                $newManifest[] = $pkg;
            }
        }

        $job->source_manifest = $newManifest;
        $job->status = JobStatus::PREFLIGHT->value;
        $job->save();

        try {
            $orchestrator->process($job);
        } catch (\Exception $e) {
            // Background processing
        }

        return redirect()->route('migrations.logs', $job->id)->with('success', 'Packages mapped! Provisioning started.');
    }

    public function showLogs(string $id)
    {
        $job = MigrationJob::findOrFail($id);
        return view('migrations.logs', compact('job'));
    }

    public function destroy(string $id)
    {
        $job = MigrationJob::findOrFail($id);
        $job->delete();
        return redirect()->route('dashboard')->with('success', 'Migration job deleted successfully.');
    }
}
