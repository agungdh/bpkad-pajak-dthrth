<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop unique index on uuid, create hash index instead
        Schema::table('kode_pajaks', function (Blueprint $table) {
            $table->dropUnique('kode_pajaks_uuid_unique');
        });

        DB::statement('CREATE INDEX kode_pajaks_uuid_hash ON kode_pajaks USING hash (uuid)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS kode_pajaks_uuid_hash');

        Schema::table('kode_pajaks', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }
};
