<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tandons', function (Blueprint $table) {
            $table->tinyInteger('pump_status')
                ->nullable()
                ->after('height_warning')
                ->comment('Pump status: 1=ON, 0=OFF, NULL=UNKNOWN');
        });
    }

    public function down(): void
    {
        Schema::table('tandons', function (Blueprint $table) {
            $table->dropColumn('pump_status');
        });
    }
};
