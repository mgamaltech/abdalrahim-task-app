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
        Schema::create('label_task', function (Blueprint $table) {
            $table->unsignedInteger('label_id');
            $table->unsignedInteger('task_id');

            $table->primary(['label_id', 'task_id']);

            $table->foreign('label_id', 'fk_label_task_label')
                ->references('id')->on('labels')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('task_id', 'fk_label_task_task')
                ->references('id')->on('tasks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->index('task_id', 'idx_label_task_task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_task');
    }
};
