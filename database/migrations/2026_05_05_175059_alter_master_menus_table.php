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
        Schema::table('master_menus', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('stok');
            
            $table->string('kodemenu')->nullable()->after('id');
            $table->foreignId('angkringan_id')->nullable()->after('kodemenu')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_menus', function (Blueprint $table) {
            $table->dropForeign(['angkringan_id']);
            $table->dropColumn('angkringan_id');
            $table->dropColumn('kodemenu');
            
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('stok')->default(0);
        });
    }
};
