<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationConnection extends Model
{
    use HasUuids;

    protected $fillable = ['organization_id', 'name', 'provider', 'credential_id', 'hostname'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }
}
