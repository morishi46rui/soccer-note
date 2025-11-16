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
        Schema::create('team_user', function (Blueprint $table) {
            $table->id()->comment('主キー');
            $table->foreignId('team_id')->constrained()->onDelete('cascade')->comment('チームID');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('ユーザーID');
            $table->foreignId('role_id')->constrained()->onDelete('restrict')->comment('ロールID');
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
            $table->index('team_id');
            $table->index('user_id');
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
