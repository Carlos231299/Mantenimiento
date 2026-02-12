<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('inventory_code')->unique(); // PC-01-001
            $table->string('ip_address')->nullable();
            $table->text('specifications')->nullable();
            $table->enum('status', ['operational', 'maintenance', 'faulty'])->default('operational');
            $table->boolean('is_teacher_pc')->default(false);
            $table->integer('position_index')->default(0); // Para ordenar en la grilla
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
