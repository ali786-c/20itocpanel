<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasUuids;

    protected $fillable = ['name'];

    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class);
    }

    public function sourceConnections(): HasMany
    {
        return $this->hasMany(SourceConnection::class);
    }

    public function destinationConnections(): HasMany
    {
        return $this->hasMany(DestinationConnection::class);
    }

    public function migrationJobs(): HasMany
    {
        return $this->hasMany(MigrationJob::class);
    }

    public function auditEvents(): HasMany
    {
        return $this->hasMany(AuditEvent::class);
    }
}
