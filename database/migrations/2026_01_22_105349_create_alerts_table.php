<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tandon_id')
                  ->constrained('tandons')
                  ->cascadeOnDelete();

            $table->string('type');
            // contoh: LOW_LEVEL, LEAK, OVERUSE

            $table->string('message');
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
