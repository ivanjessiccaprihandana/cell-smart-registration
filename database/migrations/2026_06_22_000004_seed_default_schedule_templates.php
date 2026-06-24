<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $programs = DB::table('programs')
            ->where('status', 'active')
            ->get(['id', 'name']);

        $classTypePrograms = ['English for Kids', 'English for Teens', 'English for Adult'];
        $slots = [
            [
                'days' => [1, 3],
                'start_time' => '15:00',
                'end_time' => '16:00',
                'room' => 'Ruang A',
            ],
            [
                'days' => [2, 4],
                'start_time' => '16:15',
                'end_time' => '17:15',
                'room' => 'Ruang B',
            ],
            [
                'days' => [5, 6],
                'start_time' => '19:00',
                'end_time' => '20:00',
                'room' => 'Ruang C',
            ],
        ];
        $now = now();

        foreach ($programs as $program) {
            $classTypes = in_array($program->name, $classTypePrograms, true)
                ? ['Reguler', 'Private', 'Conversation']
                : [null];

            foreach ($classTypes as $classType) {
                foreach ($slots as $slot) {
                    $exists = DB::table('schedule_templates')
                        ->where('program_id', $program->id)
                        ->where('class_type', $classType)
                        ->where('start_time', $slot['start_time'])
                        ->where('end_time', $slot['end_time'])
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('schedule_templates')->insert([
                        'program_id' => $program->id,
                        'tutor_id' => null,
                        'class_type' => $classType,
                        'level' => null,
                        'days' => json_encode($slot['days']),
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'room' => $slot['room'],
                        'notes' => 'Pilihan jadwal default untuk calon siswa.',
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        DB::table('schedule_templates')
            ->where('notes', 'Pilihan jadwal default untuk calon siswa.')
            ->delete();
    }
};
