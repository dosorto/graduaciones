<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('invitation_qr_position', 32)->default('right-bottom')->after('invitation_background_path');
            $table->unsignedSmallInteger('invitation_qr_size')->default(180)->after('invitation_qr_position');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['invitation_qr_position', 'invitation_qr_size']);
        });
    }
};
