<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skpds', function (Blueprint $table) {
            // Drop existing timestamps columns to replace with epoch
            $table->dropTimestamps();
        });

        Schema::table('skpds', function (Blueprint $table) {
            // Add epoch-based timestamps
            $table->unsignedBigInteger('created_at')->nullable();
            $table->unsignedBigInteger('updated_at')->nullable();

            // Add audit columns without foreign keys
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skpds', function (Blueprint $table) {
            // Drop epoch-based columns
            $table->dropColumn(['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by']);
        });

        Schema::table('skpds', function (Blueprint $table) {
            // Restore original timestamps
            $table->timestamps();
        });
    }
};
