<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengiriman')->unique();
            $table->foreignId('courier_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->enum('status', [
                'dimasak',
                'dikemas',
                'dalam_perjalanan',
                'sudah_sampai',
                'diterima_guru',
                'diterima_murid',
                'selesai'
            ])->default('dimasak');
            $table->integer('total_portions')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('delivery_date');
            $table->timestamps();
        });

        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->float('accuracy')->nullable();
            $table->float('speed')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['delivery_id', 'recorded_at']);
        });

        Schema::create('confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->boolean('teacher_confirmed')->default(false);
            $table->timestamp('teacher_confirmed_at')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('parent_confirmed')->default(false);
            $table->timestamp('parent_confirmed_at')->nullable();
            $table->boolean('eaten_status')->default(false);
            $table->timestamp('eaten_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['delivery_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confirmations');
        Schema::dropIfExists('delivery_tracking');
        Schema::dropIfExists('deliveries');
    }
};
