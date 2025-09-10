<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {Schema::create('geofence_violations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->foreignId('attendance_log_id')->constrained()->onDelete('cascade');
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->string('device_info')->nullable();
        $table->timestamp('violation_time');
        $table->string('violation_type')->default('clock_out'); // future-proofing
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geofence_violations');
    }
};
