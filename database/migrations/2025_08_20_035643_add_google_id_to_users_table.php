<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'after' hanya berfungsi di MySQL; di PostgreSQL akan diabaikan.
            $table->string('google_id')->nullable()->unique(); // ->after('email') boleh dihapus
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // sebutkan nama index uniknya
            $table->dropUnique('users_google_id_unique');
            $table->dropColumn('google_id');
        });
    }
};