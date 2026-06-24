<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->timestamp('reentry_enabled_at')->nullable()->after('validated_by_user_id');
            $table->foreignId('reentry_enabled_by_user_id')->nullable()->after('reentry_enabled_at')->constrained('users')->nullOnDelete();
            $table->timestamp('reentry_used_at')->nullable()->after('reentry_enabled_by_user_id');
            $table->foreignId('reentry_validated_by_user_id')->nullable()->after('reentry_used_at')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('reentry_count')->default(0)->after('reentry_validated_by_user_id');

            $table->index(['event_id', 'reentry_enabled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->dropIndex(['event_id', 'reentry_enabled_at']);
            $table->dropConstrainedForeignId('reentry_enabled_by_user_id');
            $table->dropConstrainedForeignId('reentry_validated_by_user_id');
            $table->dropColumn([
                'reentry_enabled_at',
                'reentry_used_at',
                'reentry_count',
            ]);
        });
    }
};
