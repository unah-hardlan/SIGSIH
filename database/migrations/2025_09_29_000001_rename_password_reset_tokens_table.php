<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('password_reset_tokens') && ! Schema::hasTable('tbl_ms_token_recuperacion')) {
            Schema::rename('password_reset_tokens', 'tbl_ms_token_recuperacion');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_ms_token_recuperacion') && ! Schema::hasTable('password_reset_tokens')) {
            Schema::rename('tbl_ms_token_recuperacion', 'password_reset_tokens');
        }
    }
};
