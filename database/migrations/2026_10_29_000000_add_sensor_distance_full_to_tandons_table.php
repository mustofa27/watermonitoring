<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tandons', function (Blueprint $table) {
            $table->decimal('sensor_distance_full', 8, 3)
                ->nullable()
                ->after('height_warning')
                ->comment('Jarak sensor ke permukaan air saat tandon penuh (m)');
        });
    }

    public function down(): void
    {
        Schema::table('tandons', function (Blueprint $table) {
            $table->dropColumn('sensor_distance_full');
        });
    }
};
