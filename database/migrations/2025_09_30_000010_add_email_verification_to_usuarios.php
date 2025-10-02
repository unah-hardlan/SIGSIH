<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_ms_usuario', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_ms_usuario', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('correo_electronico');
            }
            if (!Schema::hasColumn('tbl_ms_usuario', 'email_verification_token')) {
                $table->string('email_verification_token', 64)->nullable()->after('email_verified_at')->index();
            }
            if (!Schema::hasColumn('tbl_ms_usuario', 'email_verification_sent_at')) {
                $table->timestamp('email_verification_sent_at')->nullable()->after('email_verification_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_ms_usuario', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_ms_usuario', 'email_verification_sent_at')) {
                $table->dropColumn('email_verification_sent_at');
            }
            if (Schema::hasColumn('tbl_ms_usuario', 'email_verification_token')) {
                $table->dropColumn('email_verification_token');
            }
            if (Schema::hasColumn('tbl_ms_usuario', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};