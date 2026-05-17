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
        Schema::table('users', function (Blueprint $table) {
            // Drop unique constraint from email temporarily
            $table->dropUnique(['email']);

            // Add new fields
            $table->string('username')->unique()->after('email');
            $table->string('lokasi')->nullable()->after('remember_token');
            $table->string('owner')->nullable()->after('lokasi');
            $table->string('no_telpon', 20)->nullable()->after('owner');
            $table->string('flag')->default('tidak aktif')->after('no_telpon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'lokasi', 'owner', 'no_telpon', 'flag']);
        });
    }
};
