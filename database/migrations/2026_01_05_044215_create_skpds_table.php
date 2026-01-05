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
        Schema::create('skpds', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('nama');
            $table->timestamps();
        });

        // Create hash index for UUID
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX skpds_uuid_hash ON skpds USING hash (uuid);');
        } else {
            Schema::table('skpds', function (Blueprint $table) {
                $table->index('uuid', 'skpds_uuid_hash');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS skpds_uuid_hash;');
        Schema::dropIfExists('skpds');
    }
};
