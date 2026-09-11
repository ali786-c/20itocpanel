<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EndpointRegistry extends Model
{
    use HasUuids;

    protected $table = 'endpoint_registry';

    protected $fillable = [
        'provider',
        'api_family',
        'method',
        'path_template',
        'feature',
        'required_scope',
        'request_schema_json',
        'response_schema_json',
        'risk_level',
        'enabled'
    ];

    protected $casts = [
        'request_schema_json' => 'array',
        'response_schema_json' => 'array',
        'enabled' => 'boolean',
    ];
}
