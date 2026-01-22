<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tandons', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type'); 
            // contoh: "PUSAT", "GEDUNG"

            $table->string('building_name')->nullable();

            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('tandons')
                  ->nullOnDelete();

            $table->decimal('cross_section_area', 10, 4)
                  ->comment('Luas penampang tandon (m2)');

            $table->decimal('height_max', 8, 3);
            $table->decimal('height_min', 8, 3);
            $table->decimal('height_warning', 8, 3);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tandons');
    }
};
