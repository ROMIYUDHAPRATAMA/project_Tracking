<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');                                // Tugas
            $table->string('pic')->nullable();                      // PIC
            $table->enum('status', ['todo','in_progress','review','done'])->default('todo');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);    // 0..100
            $table->string('outcome')->nullable();                  // Hasil akhir
            $table->text('activity')->nullable();                   // Kegiatan
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
