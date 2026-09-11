<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('source_connection_id');
            $table->uuid('destination_connection_id');
            $table->string('status'); // draft, discovering, etc.
            $table->json('source_manifest')->nullable();
            $table->timestamps();
            
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('source_connection_id')->references('id')->on('source_connections')->onDelete('cascade');
            $table->foreign('destination_connection_id')->references('id')->on('destination_connections')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_jobs');
    }
};
