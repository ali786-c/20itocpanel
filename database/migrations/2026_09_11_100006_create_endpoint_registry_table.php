<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('endpoint_registry', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider');
            $table->string('api_family');
            $table->string('method');
            $table->string('path_template');
            $table->string('feature');
            $table->string('required_scope')->nullable();
            $table->json('request_schema_json')->nullable();
            $table->json('response_schema_json')->nullable();
            $table->string('risk_level'); // read, write_reversible, etc.
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('endpoint_registry');
    }
};
