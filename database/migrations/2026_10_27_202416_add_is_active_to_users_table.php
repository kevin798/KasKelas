<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true);
        });

        DB::table('users')
            ->where('role', 'admin')
            ->orderBy('id')
            ->get(['id', 'kas_class_id'])
            ->groupBy('kas_class_id')
            ->each(fn ($users) => DB::table('users')->where('id', $users->first()->id)->update(['role' => 'super_admin']));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_active');
        });
    }
};
