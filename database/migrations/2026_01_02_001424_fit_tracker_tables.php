<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('check_ins', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('checked_in_at');
            $table->timestamps();
        });

        Schema::create('activities', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins');
            $table->string('type');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->decimal('distance', 10, 2)->nullable(); // in kilometers
            $table->decimal('calories_burned')->nullable();
            $table->integer('steps')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('activities');
    }
};
