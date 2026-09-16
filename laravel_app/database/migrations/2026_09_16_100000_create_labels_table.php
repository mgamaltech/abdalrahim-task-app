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
        Schema::create('labels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique('uq_labels_name');
        });

        // Raw CHECK: see note in the users migration.
        DB::statement("ALTER TABLE labels ADD CONSTRAINT chk_labels_name_not_blank CHECK (TRIM(name) <> '')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labels');
    }
};
