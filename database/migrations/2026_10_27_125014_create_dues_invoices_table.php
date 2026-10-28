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
    {
        Schema::create('dues_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kas_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('period');
            $table->unsignedBigInteger('amount');
            $table->date('due_on')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues_invoices');
    }
};
