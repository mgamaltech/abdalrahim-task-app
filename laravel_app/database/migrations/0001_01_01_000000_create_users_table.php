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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique('uq_users_email');
            $table->timestamp('created_at')->useCurrent();
        });

        // The schema builder has no CHECK support, so these are raw MySQL/MariaDB
        // statements. They run unconditionally: a database that cannot hold them
        // must fail here rather than silently accept blank values.
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_name_not_blank CHECK (TRIM(name) <> '')");
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_email_not_blank CHECK (TRIM(email) <> '')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
