<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kas_classes', function (Blueprint $table): void {
            $table->string('class_code', 10)->nullable()->unique();
        });

        DB::table('kas_classes')->whereNull('class_code')->orderBy('id')->get(['id'])->each(function (object $class): void {
            do {
                $code = Str::upper(Str::random(10));
            } while (DB::table('kas_classes')->where('class_code', $code)->exists());

            DB::table('kas_classes')->where('id', $class->id)->update(['class_code' => $code]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_classes', function (Blueprint $table): void {
            $table->dropUnique(['class_code']);
            $table->dropColumn('class_code');
        });
    }
};
