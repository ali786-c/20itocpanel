<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEvent extends Model
{
    use HasUuids;

    protected $fillable = [
        'organization_id',
        'migration_job_id',
        'event_type',
        'message',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function migrationJob(): BelongsTo
    {
        return $this->belongsTo(MigrationJob::class);
    }
}
