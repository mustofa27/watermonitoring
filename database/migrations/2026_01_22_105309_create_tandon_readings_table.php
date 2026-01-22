<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tandon_readings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tandon_id')
                  ->constrained('tandons')
                  ->cascadeOnDelete();

            $table->decimal('water_height', 8, 3)
                  ->comment('Ketinggian air (cm atau m)');

            $table->decimal('water_volume', 12, 3)
                  ->comment('Volume air (liter atau m3)');

            $table->timestamp('recorded_at');

            $table->timestamps();

            $table->index(['tandon_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tandon_readings');
    }
};
