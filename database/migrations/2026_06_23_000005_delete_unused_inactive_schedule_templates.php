<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $usedTemplateIds = DB::table('schedule_preferences')
            ->pluck('schedule_template_id')
            ->merge(DB::table('class_schedules')->whereNotNull('schedule_template_id')->pluck('schedule_template_id'))
            ->filter()
            ->unique()
            ->values();

        DB::table('schedule_templates')
            ->where('is_active', false)
            ->when($usedTemplateIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $usedTemplateIds))
            ->delete();
    }

    public function down(): void
    {
        // Jadwal lama yang dihapus adalah data arsip yang belum dipakai siswa.
    }
};
