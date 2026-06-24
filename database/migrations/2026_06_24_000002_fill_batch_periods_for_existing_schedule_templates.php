<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('schedule_templates')
            ->whereNull('batch_name')
            ->update([
                'batch_name' => 'Batch ' . $now->copy()->addMonth()->translatedFormat('F Y'),
                'registration_start_date' => $now->toDateString(),
                'registration_end_date' => $now->copy()->addDays(7)->toDateString(),
                'learning_start_date' => $now->copy()->addDays(8)->toDateString(),
                'learning_end_date' => $now->copy()->addDays(8)->addMonth()->subDay()->toDateString(),
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        DB::table('schedule_templates')
            ->where('batch_name', 'like', 'Batch %')
            ->update([
                'batch_name' => null,
                'registration_start_date' => null,
                'registration_end_date' => null,
                'learning_start_date' => null,
                'learning_end_date' => null,
            ]);
    }
};
