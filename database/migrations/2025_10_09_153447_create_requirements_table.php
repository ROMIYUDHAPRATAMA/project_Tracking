<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('requirements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->enum('type', ['FR','NFR'])->default('FR');
            $t->enum('priority', ['Low','Medium','High'])->default('Medium');
            $t->enum('status', ['Planned','In Progress','Done'])->default('Planned');
            $t->text('acceptance_criteria')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('requirements');
    }
};
