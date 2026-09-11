<?php

namespace App\Jobs\Migration;

use App\Models\MigrationJob;
use App\Enums\JobStatus;
use App\Services\Migration\MigrationOrchestrator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FileTransferWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large sites

    protected MigrationJob $jobModel;

    public function __construct(MigrationJob $jobModel)
    {
        $this->jobModel = $jobModel;
    }

    public function handle(MigrationOrchestrator $orchestrator): void
    {
        Log::info("Starting File Transfer for Job ID: {$this->jobModel->id}");

        try {
            // Placeholder: Initialize SFTP stream from 20i Source to cPanel Destination.
            // ... SFTP Streaming Logic Here ...
            
            Log::info("File transfer complete for Job ID: {$this->jobModel->id}");

            // Transition job state after success
            $this->jobModel->status = JobStatus::VERIFYING_INITIAL->value;
            $this->jobModel->save();

            // Potentially re-trigger the orchestrator here
            $orchestrator->process($this->jobModel);

        } catch (\Exception $e) {
            Log::error("File transfer failed for Job ID {$this->jobModel->id}: " . $e->getMessage());
            $this->jobModel->status = JobStatus::FAILED->value;
            $this->jobModel->save();
            throw $e; // Allow Laravel queue to handle retries
        }
    }
}
