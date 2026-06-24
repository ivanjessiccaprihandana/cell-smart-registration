<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $rooms = DB::table('class_rooms')->pluck('id', 'name');
        $programs = DB::table('programs')
            ->where('status', 'active')
            ->get(['id', 'name', 'category']);
        $now = now();

        $englishSlots = [
            ['room' => 'English Room 1', 'days' => [1, 3], 'start_time' => '15:00', 'end_time' => '16:00'],
            ['room' => 'English Room 2', 'days' => [2, 4], 'start_time' => '16:15', 'end_time' => '17:15'],
            ['room' => 'English Room 3', 'days' => [5, 6], 'start_time' => '19:00', 'end_time' => '20:00'],
        ];

        $bimbelSlots = [
            ['room' => 'Bimbel Room 1', 'days' => [1, 3], 'start_time' => '15:00', 'end_time' => '16:00'],
            ['room' => 'Bimbel Room 2', 'days' => [2, 4], 'start_time' => '16:15', 'end_time' => '17:15'],
            ['room' => 'Bimbel Room 3', 'days' => [5, 6], 'start_time' => '19:00', 'end_time' => '20:00'],
        ];

        $classTypePrograms = ['English for Kids', 'English for Teens', 'English for Adult'];

        foreach ($programs as $program) {
            $isBimbel = Str::lower((string) $program->category) === 'bimbel'
                || Str::startsWith(Str::lower($program->name), 'bimbel');
            $classTypes = in_array($program->name, $classTypePrograms, true)
                ? ['Reguler', 'Private', 'Conversation']
                : [null];
            $slots = $isBimbel ? $bimbelSlots : $englishSlots;

            foreach ($classTypes as $classType) {
                foreach ($slots as $slot) {
                    $roomId = $rooms[$slot['room']] ?? null;
                    $maxStudents = $classType === 'Private' ? 1 : 8;
                    $tutorId = DB::table('tutors')
                        ->where('program_id', $program->id)
                        ->whereNull('level')
                        ->where('is_active', true)
                        ->orderBy('id')
                        ->value('id');

                    $existingId = DB::table('schedule_templates')
                        ->where('program_id', $program->id)
                        ->where('class_type', $classType)
                        ->where('start_time', $slot['start_time'])
                        ->where('end_time', $slot['end_time'])
                        ->value('id');

                    $payload = [
                        'program_id' => $program->id,
                        'tutor_id' => $tutorId,
                        'class_room_id' => $roomId,
                        'class_type' => $classType,
                        'level' => null,
                        'days' => json_encode($slot['days']),
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'room' => $slot['room'],
                        'max_students' => $maxStudents,
                        'notes' => $classType === 'Private'
                            ? 'Jadwal private 1 siswa per kelas sesuai ketentuan CELL.'
                            : 'Jadwal belajar 2 kali seminggu dengan kapasitas maksimal 8 siswa.',
                        'is_active' => true,
                        'updated_at' => $now,
                    ];

                    if ($existingId) {
                        DB::table('schedule_templates')
                            ->where('id', $existingId)
                            ->update($payload);

                        continue;
                    }

                    DB::table('schedule_templates')->insert([
                        ...$payload,
                        'created_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        DB::table('schedule_templates')
            ->whereIn('notes', [
                'Jadwal private 1 siswa per kelas sesuai ketentuan CELL.',
                'Jadwal belajar 2 kali seminggu dengan kapasitas maksimal 8 siswa.',
            ])
            ->update([
                'notes' => 'Pilihan jadwal default untuk calon siswa.',
                'updated_at' => now(),
            ]);
    }
};
