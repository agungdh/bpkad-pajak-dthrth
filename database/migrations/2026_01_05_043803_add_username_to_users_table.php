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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('uuid');
            $table->string('email')->nullable()->change();
        });

        // Populate username for existing users from their email
        DB::table('users')->whereNull('username')->get()->each(function ($user) {
            $username = explode('@', $user->email)[0];
            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        });

        // Make username non-nullable and unique
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};
