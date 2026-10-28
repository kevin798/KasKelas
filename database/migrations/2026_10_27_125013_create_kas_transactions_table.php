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
        Schema::create('kas_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kas_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('type', ['income', 'expense']);
            $table->string('category');
            $table->string('description');
            $table->unsignedBigInteger('amount');
            $table->date('occurred_on');
            $table->string('receipt_path')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_transactions');
    }
};
