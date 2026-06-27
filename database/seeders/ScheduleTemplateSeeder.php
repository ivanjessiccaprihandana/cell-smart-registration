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

        $obsoleteProgramIds = Program::whereIn('name', ['English Conversation', 'TOEIC', 'TOEFL'])->pluck('id');
        DB::table('schedule_templates')->whereIn('program_id', $obsoleteProgramIds)->delete();

        $mainProgramIds = Program::whereIn('name', ['English for Kids', 'English for Teens', 'English for Adult'])
            ->pluck('id');
        DB::table('schedule_templates')
            ->whereIn('program_id', $mainProgramIds)
            ->where(function ($query) {
                $query->where('class_type', 'Private')
                    ->orWhereNotNull('private_package');
            })
            ->delete();

        $templates = [
            ['English for Kids', 'Reguler', null, 'English Room 1', [1, 3], '15:00', '16:00', 8],
            ['English for Teens', 'Reguler', null, 'English Room 2', [2, 4], '15:00', '16:00', 8],
            ['English for Adult', 'Reguler', null, 'English Room 3', [5, 6], '16:15', '17:15', 8],

            ['English for Adult', 'Private', 'Conversation', 'English Room 1', [1, 3], '19:00', '20:00', 1],
            ['English for Adult', 'Private', 'TOEFL Preparation', 'English Room 2', [2, 4], '19:00', '20:00', 1],
            ['English for Adult', 'Private', 'TOEIC Preparation', 'English Room 3', [5, 6], '19:00', '20:00', 1],

            ['BIMBEL TK', null, null, 'Bimbel Room 1', [1, 3], '13:00', '14:00', 8],
            ['BIMBEL SD', null, null, 'Bimbel Room 2', [1, 3], '15:00', '16:00', 8],
            ['BIMBEL SMP', null, null, 'Bimbel Room 3', [2, 4], '16:15', '17:15', 8],
            ['BIMBEL SMA', null, null, 'Bimbel Room 3', [5, 6], '19:00', '20:00', 8],
        ];

        foreach ($templates as [$programName, $classType, $privatePackage, $roomName, $days, $startTime, $endTime, $maxStudents]) {
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
            $isPrivate = $classType === 'Private';
            $templateBatchName = match (true) {
                $isPrivate => $privatePackage . ' Private - ' . $learningStart->translatedFormat('F Y'),
                $isBimbel => $programName . ' Batch ' . $learningStart->translatedFormat('F Y'),
                default => $batchName,
            };

            DB::table('schedule_templates')->updateOrInsert(
                [
                    'program_id' => $programId,
                    'class_type' => $classType,
                    'private_package' => $privatePackage,
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
                    'learning_end_date' => $isPrivate
                        ? $learningStart->copy()->addMonthsNoOverflow(3)->endOfMonth()->toDateString()
                        : $learningEnd->toDateString(),
                    'days' => json_encode($days),
                    'room' => $roomName,
                    'max_students' => $maxStudents,
                    'notes' => $isPrivate
                        ? 'Paket private 25 pertemuan. Satu siswa dalam satu kelas sesuai ketentuan CELL.'
                        : ($isBimbel
                            ? 'Batch BIMBEL bulanan. Belajar 2 kali seminggu dan tetap dapat berjalan walaupun peserta baru 1 siswa.'
                            : 'Jadwal belajar batch berjalan 2 kali seminggu. Kelas tetap dapat berjalan walaupun peserta baru 1 siswa.'),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
