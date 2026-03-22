<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();

            // Service type
            $table->enum('service_type', [
                'kitchen_planning',
                'wardrobe_planning',
                'bedroom_planning',
                'full_room_layout',
                'interior_planning',
                'general_consultation',
            ]);

            // Schedule
            $table->date('appointment_date');
            $table->time('appointment_time');

            // Customer info (may differ from profile)
            $table->string('full_name');
            $table->string('phone');
            $table->string('email');

            // Details
            $table->text('notes')->nullable();
            $table->string('room_size')->nullable(); // e.g. "3m x 4m"

            // Status flow: pending → confirmed → completed / cancelled
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                  ->default('pending');

            $table->text('staff_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};