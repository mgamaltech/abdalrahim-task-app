<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('priority', 20)->default('medium');
            $table->boolean('done')->default(false);

            $table->unsignedInteger('user_id')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id', 'fk_tasks_user')
                ->references('id')->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->index(['user_id', 'done'], 'idx_tasks_user_done');
        });

        // Raw CHECKs: see note in the users migration.
        DB::statement("ALTER TABLE tasks ADD CONSTRAINT chk_tasks_title_not_blank CHECK (TRIM(title) <> '')");
        DB::statement("ALTER TABLE tasks ADD CONSTRAINT chk_tasks_priority CHECK (priority IN ('low', 'medium', 'high'))");
        DB::statement("ALTER TABLE tasks ADD CONSTRAINT chk_tasks_completed_at CHECK (done = FALSE OR completed_at IS NOT NULL)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
