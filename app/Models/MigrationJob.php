<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MigrationJob extends Model
{
    use HasUuids;

    protected $fillable = [
        'organization_id',
        'source_connection_id',
        'destination_connection_id',
        'status',
        'source_manifest'
    ];

    protected $casts = [
        'source_manifest' => 'array',
        'logs' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sourceConnection(): BelongsTo
    {
        return $this->belongsTo(SourceConnection::class);
    }

    public function destinationConnection(): BelongsTo
    {
        return $this->belongsTo(DestinationConnection::class);
    }
}
