<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::rename('notifications', 'tbl_notificacion');
        } elseif (!Schema::hasTable('tbl_notificacion')) {
            Schema::create('tbl_notificacion', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_notificacion') && !Schema::hasTable('notifications')) {
            Schema::rename('tbl_notificacion', 'notifications');
        }
    }
};
