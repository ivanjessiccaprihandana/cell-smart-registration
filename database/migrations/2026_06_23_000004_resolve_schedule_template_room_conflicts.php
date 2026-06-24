<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $programs = DB::table('programs')->pluck('id', 'name');
        $rooms = DB::table('class_rooms')->pluck('id', 'name');

        DB::table('schedule_templates')->update([
            'is_active' => false,
            'updated_at' => $now,
        ]);

        $templates = [
            ['English for Kids', 'Reguler', 'English Room 1', [1, 3], '15:00', '16:00', 8],
            ['English for Kids', 'Private', 'English Room 1', [2, 4], '16:15', '17:15', 1],
            ['English for Kids', 'Conversation', 'English Room 1', [5, 6], '19:00', '20:00', 8],

            ['English for Teens', 'Reguler', 'English Room 2', [1, 3], '15:00', '16:00', 8],
            ['English for Teens', 'Private', 'English Room 2', [2, 4], '16:15', '17:15', 1],
            ['English for Teens', 'Conversation', 'English Room 2', [5, 6], '19:00', '20:00', 8],

            ['English for Adult', 'Reguler', 'English Room 3', [1, 3], '15:00', '16:00', 8],
            ['English for Adult', 'Private', 'English Room 3', [2, 4], '16:15', '17:15', 1],
            ['English for Adult', 'Conversation', 'English Room 3', [5, 6], '19:00', '20:00', 8],

            ['English Conversation', null, 'English Room 1', [2, 4], '19:00', '20:00', 8],
            ['TOEIC', null, 'English Room 2', [1, 3], '19:00', '20:00', 8],
            ['TOEFL', null, 'English Room 3', [2, 4], '19:00', '20:00', 8],

            ['BIMBEL TK', null, 'Bimbel Room 1', [1, 3], '13:00', '14:00', 8],
            ['BIMBEL SD', null, 'Bimbel Room 2', [1, 3], '15:00', '16:00', 8],
            ['BIMBEL SMP', null, 'Bimbel Room 3', [2, 4], '16:15', '17:15', 8],
            ['BIMBEL SMA', null, 'Bimbel Room 3', [5, 6], '19:00', '20:00', 8],
        ];

        foreach ($templates as [$programName, $classType, $roomName, $days, $startTime, $endTime, $maxStudents]) {
            $programId = $programs[$programName] ?? null;
            $roomId = $rooms[$roomName] ?? null;

            if (!$programId || !$roomId) {
                continue;
            }

            $tutorId = DB::table('tutors')
                ->where('program_id', $programId)
                ->whereNull('level')
                ->where('is_active', true)
                ->orderBy('id')
                ->value('id');

            $payload = [
                'program_id' => $programId,
                'tutor_id' => $tutorId,
                'class_room_id' => $roomId,
                'class_type' => $classType,
                'level' => null,
                'days' => json_encode($days),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'room' => $roomName,
                'max_students' => $maxStudents,
                'notes' => $classType === 'Private'
                    ? 'Jadwal private 1 siswa per kelas sesuai ketentuan CELL.'
                    : 'Jadwal belajar 2 kali seminggu tanpa bentrok ruang kelas.',
                'is_active' => true,
                'updated_at' => $now,
            ];

            $existingId = DB::table('schedule_templates')
                ->where('program_id', $programId)
                ->where('class_type', $classType)
                ->where('class_room_id', $roomId)
                ->where('start_time', $startTime)
                ->where('end_time', $endTime)
                ->value('id');

            if ($existingId) {
                DB::table('schedule_templates')->where('id', $existingId)->update($payload);

                continue;
            }

            DB::table('schedule_templates')->insert([
                ...$payload,
                'created_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('schedule_templates')
            ->whereIn('notes', [
                'Jadwal belajar 2 kali seminggu tanpa bentrok ruang kelas.',
                'Jadwal private 1 siswa per kelas sesuai ketentuan CELL.',
            ])
            ->update([
                'is_active' => true,
                'updated_at' => now(),
            ]);
    }
};
