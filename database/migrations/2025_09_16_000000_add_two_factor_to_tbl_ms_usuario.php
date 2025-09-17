<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_ms_usuario', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_ms_usuario', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable();
            }
            if (!Schema::hasColumn('tbl_ms_usuario', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable();
            }
            if (!Schema::hasColumn('tbl_ms_usuario', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('tbl_ms_usuario', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_ms_usuario', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_ms_usuario', 'two_factor_secret')) {
                $table->dropColumn('two_factor_secret');
            }
            if (Schema::hasColumn('tbl_ms_usuario', 'two_factor_recovery_codes')) {
                $table->dropColumn('two_factor_recovery_codes');
            }
            if (Schema::hasColumn('tbl_ms_usuario', 'two_factor_confirmed_at')) {
                $table->dropColumn('two_factor_confirmed_at');
            }
            if (Schema::hasColumn('tbl_ms_usuario', 'two_factor_enabled')) {
                $table->dropColumn('two_factor_enabled');
            }
        });
    }
};
