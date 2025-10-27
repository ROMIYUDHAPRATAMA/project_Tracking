<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('design_specs', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel project & requirement
            $table->foreignId('project_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('requirement_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Kolom utama
            $table->enum('artifact_type', ['UI', 'API', 'DB', 'Flow'])->default('UI');
            $table->string('artifact_name');
            $table->string('reference_url')->nullable();
            $table->text('rationale')->nullable();
            $table->enum('status', ['Draft', 'Review', 'Approved'])->default('Draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_specs');
    }
};
