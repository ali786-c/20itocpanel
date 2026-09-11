<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MigrationJob;
use App\Services\Migration\MigrationOrchestrator;
use App\Enums\JobStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MigrationJobController extends Controller
{
    protected MigrationOrchestrator $orchestrator;

    public function __construct(MigrationOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function index(): JsonResponse
    {
        $jobs = MigrationJob::with(['organization', 'sourceConnection', 'destinationConnection'])->get();
        return response()->json(['data' => $jobs]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|uuid|exists:organizations,id',
            'source_connection_id' => 'required|uuid|exists:source_connections,id',
            'destination_connection_id' => 'required|uuid|exists:destination_connections,id',
        ]);

        $validated['status'] = JobStatus::DRAFT->value;

        $job = MigrationJob::create($validated);

        return response()->json(['data' => $job, 'message' => 'Migration Job created successfully.'], 201);
    }

    public function show($id): JsonResponse
    {
        $job = MigrationJob::with(['organization', 'sourceConnection', 'destinationConnection'])->findOrFail($id);
        return response()->json(['data' => $job]);
    }

    public function process($id): JsonResponse
    {
        $job = MigrationJob::with(['sourceConnection.credential', 'destinationConnection.credential'])->findOrFail($id);
        
        try {
            $this->orchestrator->process($job);
            
            // Re-fetch to get updated status
            $job->refresh();
            
            return response()->json([
                'message' => 'Job processed successfully.',
                'status' => $job->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process job.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
