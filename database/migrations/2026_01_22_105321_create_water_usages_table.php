<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('water_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tandon_id')
                  ->constrained('tandons')
                  ->cascadeOnDelete();

            $table->date('usage_date');
            $table->decimal('volume_used', 12, 3);

            $table->timestamps();

            $table->unique(['tandon_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_usages');
    }
};
