<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_persona')) {
            Schema::table('tbl_persona', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_persona', 'cargo')) {
                    $table->dropColumn('cargo');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_persona')) {
            Schema::table('tbl_persona', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_persona', 'cargo')) {
                    $table->string('cargo', 50)->nullable()->after('dni');
                }
            });
        }
    }
};
