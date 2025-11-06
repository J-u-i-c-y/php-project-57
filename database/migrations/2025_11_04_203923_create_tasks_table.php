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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description')->nullable();
            $table->bigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('task_statuses');
            $table->bigInteger('creator_by_id');
            $table->foreign('creator_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('assigned_by_id')->nullable();
            $table->foreign('assigned_by_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
