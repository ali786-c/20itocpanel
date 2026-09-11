<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('name');
            $table->string('provider'); // e.g., cpanel
            $table->uuid('credential_id');
            $table->string('hostname');
            $table->timestamps();
            
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('credential_id')->references('id')->on('credentials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_connections');
    }
};
