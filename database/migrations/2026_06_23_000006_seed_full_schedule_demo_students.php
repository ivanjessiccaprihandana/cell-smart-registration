<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $templates = DB::table('schedule_templates')
            ->where('is_active', true)
            ->orderBy('program_id')
            ->orderBy('class_type')
            ->orderBy('start_time')
            ->get();

        foreach ($templates as $template) {
            $usedSeats = DB::table('schedule_preferences')
                ->where('schedule_template_id', $template->id)
                ->whereIn('status', ['pending', 'assigned'])
                ->distinct('user_id')
                ->count('user_id');
            $targetSeats = max(0, (int) $template->max_students - $usedSeats);

            for ($seat = 1; $seat <= $targetSeats; $seat++) {
                $email = sprintf('demo.full.%03d.%02d@cell.local', $template->id, $seat);
                $program = DB::table('programs')->find($template->program_id);
                $studentName = 'Demo Siswa ' . str_pad((string) $template->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad((string) $seat, 2, '0', STR_PAD_LEFT);

                $userId = DB::table('users')->where('email', $email)->value('id');

                if (!$userId) {
                    $userId = DB::table('users')->insertGetId([
                        'name' => $studentName,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'whatsapp' => '081292538501',
                        'address' => 'Data demo kelas penuh CELL',
                        'program' => (string) $template->program_id,
                        'class_type' => $template->class_type,
                        'payment_proof_path' => null,
                        'payment_status' => 'diterima',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } else {
                    DB::table('users')->where('id', $userId)->update([
                        'name' => $studentName,
                        'whatsapp' => '081292538501',
                        'address' => 'Data demo kelas penuh CELL',
                        'program' => (string) $template->program_id,
                        'class_type' => $template->class_type,
                        'payment_status' => 'diterima',
                        'updated_at' => $now,
                    ]);
                }

                DB::table('program_enrollments')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'program_id' => $template->program_id,
                        'type' => 'new',
                    ],
                    [
                        'class_type' => $template->class_type,
                        'enrolled_at' => $now,
                        'start_date' => $now->toDateString(),
                        'end_date' => $now->copy()->addMonth()->toDateString(),
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                DB::table('schedule_preferences')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'schedule_template_id' => $template->id,
                    ],
                    [
                        'priority' => 1,
                        'status' => 'assigned',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $this->createMonthlySchedules($userId, $template, $program?->name ?? 'Program CELL', $now);
            }
        }
    }

    public function down(): void
    {
        $demoUserIds = DB::table('users')
            ->where('email', 'like', 'demo.full.%@cell.local')
            ->pluck('id');

        DB::table('class_schedules')
            ->whereIn('user_id', $demoUserIds)
            ->delete();

        DB::table('program_enrollments')
            ->whereIn('user_id', $demoUserIds)
            ->delete();

        DB::table('schedule_preferences')
            ->whereIn('user_id', $demoUserIds)
            ->delete();

        DB::table('users')
            ->whereIn('id', $demoUserIds)
            ->delete();
    }

    private function createMonthlySchedules(int $userId, object $template, string $programName, Carbon $now): void
    {
        $days = collect(json_decode($template->days, true) ?: [])->map(fn ($day) => (int) $day)->all();
        $startDate = $now->copy()->startOfDay();
        $endDate = $startDate->copy()->addMonth()->subDay();
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            if (!in_array($cursor->isoWeekday(), $days, true)) {
                $cursor->addDay();
                continue;
            }

            DB::table('class_schedules')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'schedule_template_id' => $template->id,
                    'class_date' => $cursor->toDateString(),
                    'start_time' => $template->start_time,
                ],
                [
                    'tutor_id' => $template->tutor_id,
                    'class_room_id' => $template->class_room_id,
                    'program_id' => $template->program_id,
                    'class_type' => $template->class_type,
                    'session_name' => 'Demo Kelas Penuh',
                    'end_time' => $template->end_time,
                    'room' => $template->room,
                    'max_students' => $template->max_students,
                    'notes' => 'Data demo untuk melihat kondisi kelas penuh.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $cursor->addDay();
        }
    }
};
