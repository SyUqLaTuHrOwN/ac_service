<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $t) {
            if (!Schema::hasColumn('feedback','client_user_id')) {
                $t->foreignId('client_user_id')->after('report_id')
                  ->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('feedback','is_public')) {
                $t->boolean('is_public')->default(false)->after('comment');
            }
            if (!Schema::hasColumn('feedback','approved_at')) {
                $t->timestamp('approved_at')->nullable()->after('is_public');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $t) {
            if (Schema::hasColumn('feedback','approved_at')) $t->dropColumn('approved_at');
            if (Schema::hasColumn('feedback','is_public'))   $t->dropColumn('is_public');

            if (Schema::hasColumn('feedback','client_user_id')) {
                $t->dropConstrainedForeignId('client_user_id');
            }
        });
    }
};
