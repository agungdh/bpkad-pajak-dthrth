<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Populate UUID for existing users
        DB::table('users')->whereNull('uuid')->get()->each(function ($user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });

        // Make uuid non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();

            // Create hash index for fast equality lookups if supported, otherwise standard index
            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'pgsql') {
                DB::statement('CREATE INDEX users_uuid_hash ON users USING hash (uuid);');
            } else {
                $table->index('uuid', 'users_uuid_hash');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop hash index
        DB::statement('DROP INDEX IF EXISTS users_uuid_hash;');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
