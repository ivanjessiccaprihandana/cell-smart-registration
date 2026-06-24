<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $registrationStart = $now->copy()->startOfDay();
        $learningStart = $now->copy()->addMonthNoOverflow()->startOfMonth();
        $registrationEnd = $learningStart->copy()->subDay();
        $learningEnd = $learningStart->copy()->endOfMonth();
        $batchName = 'Batch ' . $learningStart->translatedFormat('F Y');

        $programs = Program::where('status', 'active')->pluck('id', 'name');
        $rooms = DB::table('class_rooms')->pluck('id', 'name');

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

            $isBimbel = str_starts_with(strtolower($programName), 'bimbel');
            $templateBatchName = $isBimbel
                ? $programName . ' Batch ' . $learningStart->translatedFormat('F Y')
                : $batchName;

            DB::table('schedule_templates')->updateOrInsert(
                [
                    'program_id' => $programId,
                    'class_type' => $classType,
                    'class_room_id' => $roomId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ],
                [
                    'tutor_id' => $tutorId,
                    'level' => null,
                    'batch_name' => $templateBatchName,
                    'registration_start_date' => $registrationStart->toDateString(),
                    'registration_end_date' => $registrationEnd->toDateString(),
                    'learning_start_date' => $learningStart->toDateString(),
                    'learning_end_date' => $learningEnd->toDateString(),
                    'days' => json_encode($days),
                    'room' => $roomName,
                    'max_students' => $maxStudents,
                    'notes' => $classType === 'Private'
                        ? 'Jadwal private 1 siswa per kelas sesuai ketentuan CELL.'
                        : ($isBimbel
                            ? 'Batch BIMBEL bulanan. Belajar 2 kali seminggu dan tetap dapat berjalan walaupun peserta baru 1 siswa.'
                            : 'Jadwal belajar batch berjalan 2 kali seminggu. Kelas tetap dapat berjalan walaupun peserta baru 1 siswa.'),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $testPrepProgramIds = Program::whereIn('name', ['TOEIC', 'TOEFL'])->pluck('id');
        DB::table('schedule_templates')->whereIn('program_id', $testPrepProgramIds)->delete();

        $firstSessionDate = $learningStart->copy();
        $daysToSaturday = (6 - $firstSessionDate->isoWeekday() + 7) % 7;
        $firstSessionDate->addDays($daysToSaturday);

        $testSessions = [
            ['TOEIC', 'TOEIC Batch Pagi', 'English Room 2', $firstSessionDate->copy(), '09:00', '12:00'],
            ['TOEIC', 'TOEIC Batch Siang', 'English Room 2', $firstSessionDate->copy(), '13:00', '16:00'],
            ['TOEIC', 'TOEIC Batch Sore', 'English Room 2', $firstSessionDate->copy()->addWeek(), '15:00', '18:00'],

            ['TOEFL', 'TOEFL Batch Pagi', 'English Room 3', $firstSessionDate->copy(), '09:00', '12:00'],
            ['TOEFL', 'TOEFL Batch Siang', 'English Room 3', $firstSessionDate->copy(), '13:00', '16:00'],
            ['TOEFL', 'TOEFL Batch Sore', 'English Room 3', $firstSessionDate->copy()->addWeek(), '15:00', '18:00'],
        ];

        foreach ($testSessions as [$programName, $batchLabel, $roomName, $sessionDate, $startTime, $endTime]) {
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

            DB::table('schedule_templates')->insert([
                'program_id' => $programId,
                'tutor_id' => $tutorId,
                'class_room_id' => $roomId,
                'class_type' => null,
                'level' => null,
                'batch_name' => $batchLabel . ' - ' . $sessionDate->translatedFormat('d F Y'),
                'registration_start_date' => $registrationStart->toDateString(),
                'registration_end_date' => $sessionDate->copy()->subDay()->toDateString(),
                'learning_start_date' => $sessionDate->toDateString(),
                'learning_end_date' => $sessionDate->toDateString(),
                'days' => json_encode([$sessionDate->isoWeekday()]),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'room' => $roomName,
                'max_students' => 8,
                'notes' => 'Batch sesi offline ' . $programName . '. Satu batch berjalan pada tanggal dan jam yang dipilih siswa.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
