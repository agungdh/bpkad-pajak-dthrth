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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing timestamps columns to replace with epoch
            $table->dropTimestamps();

            // Add epoch-based timestamps
            $table->unsignedBigInteger('created_at')->nullable()->after('remember_token');
            $table->unsignedBigInteger('updated_at')->nullable()->after('created_at');

            // Add audit columns without foreign keys
            $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_at')->nullable()->after('updated_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop epoch-based columns
            $table->dropColumn(['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by']);

            // Restore original timestamps
            $table->timestamps();
        });
    }
};
