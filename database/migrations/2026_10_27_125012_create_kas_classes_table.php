<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_classes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('initial_balance')->default(0);
            $table->unsignedBigInteger('monthly_dues')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_classes');
    }
};
