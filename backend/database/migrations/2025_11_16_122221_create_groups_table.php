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
        Schema::create('groups', function (Blueprint $table) {
            $table->id()->comment('グループID');
            $table->foreignId('team_id')->constrained()->onDelete('cascade')->comment('チームID');
            $table->string('name')->comment('グループ名');
            $table->text('description')->nullable()->comment('グループの説明');
            $table->timestamps();

            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
