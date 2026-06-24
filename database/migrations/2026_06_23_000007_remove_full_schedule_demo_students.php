<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $demoUserIds = DB::table('users')
            ->where('email', 'like', 'demo.full.%@cell.local')
            ->pluck('id');

        if ($demoUserIds->isEmpty()) {
            return;
        }

        DB::table('class_schedules')
            ->whereIn('user_id', $demoUserIds)
            ->delete();

        DB::table('schedule_preferences')
            ->whereIn('user_id', $demoUserIds)
            ->delete();

        DB::table('program_enrollments')
            ->whereIn('user_id', $demoUserIds)
            ->delete();

        DB::table('users')
            ->whereIn('id', $demoUserIds)
            ->delete();
    }

    public function down(): void
    {
        // Data demo sengaja tidak dibuat ulang saat rollback.
    }
};
