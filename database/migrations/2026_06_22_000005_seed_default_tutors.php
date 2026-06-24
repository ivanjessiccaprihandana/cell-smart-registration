<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $programs = DB::table('programs')->pluck('id', 'name');
        $now = now();

        $tutors = [
            ['English for Kids', 'Siti Nur Aisyah', null, 'Tutor utama English for Kids semua level.'],
            ['English for Kids', 'Dina Maharani', 'Beginner', 'Tutor English for Kids level Beginner.'],
            ['English for Kids', 'Rani Putri', 'Elementary', 'Tutor English for Kids level Elementary.'],

            ['English for Teens', 'Aulia Rahman', null, 'Tutor utama English for Teens semua level.'],
            ['English for Teens', 'Nadia Fitri', 'Beginner', 'Tutor English for Teens level Beginner.'],
            ['English for Teens', 'Rizky Pratama', 'Elementary', 'Tutor English for Teens level Elementary.'],
            ['English for Teens', 'Maya Larasati', 'Pre-Intermediate', 'Tutor English for Teens level Pre-Intermediate.'],
            ['English for Teens', 'Fajar Hidayat', 'Intermediate', 'Tutor English for Teens level Intermediate.'],

            ['English for Adult', 'Kevin Saputra', null, 'Tutor utama English for Adult semua level.'],
            ['English for Adult', 'Laras Wulandari', 'Pre-Intermediate', 'Tutor English for Adult level Pre-Intermediate.'],
            ['English for Adult', 'Arif Nugroho', 'Intermediate', 'Tutor English for Adult level Intermediate.'],
            ['English for Adult', 'Mira Anggraini', 'Upper-Intermediate', 'Tutor English for Adult level Upper-Intermediate.'],

            ['English Conversation', 'Dewi Kartika', null, 'Tutor utama English Conversation.'],
            ['English Conversation', 'Gilang Mahendra', 'Intermediate', 'Tutor Conversation level Intermediate.'],
            ['English Conversation', 'Tania Safitri', 'Upper-Intermediate', 'Tutor Conversation level Upper-Intermediate.'],

            ['TOEIC', 'Bima Santoso', null, 'Tutor utama persiapan TOEIC.'],
            ['TOEIC', 'Hana Prameswari', 'Upper-Intermediate', 'Tutor TOEIC level Upper-Intermediate.'],

            ['TOEFL', 'Dimas Aditya', null, 'Tutor utama persiapan TOEFL.'],
            ['TOEFL', 'Naufal Akbar', 'Upper-Intermediate', 'Tutor TOEFL level Upper-Intermediate.'],
            ['TOEFL', 'Citra Amelia', 'Advanced', 'Tutor TOEFL level Advanced.'],

            ['BIMBEL TK', 'Indah Permata', null, 'Tutor BIMBEL TK.'],
            ['BIMBEL SD', 'Yusuf Maulana', null, 'Tutor BIMBEL SD.'],
            ['BIMBEL SMP', 'Putri Ramadhani', null, 'Tutor BIMBEL SMP.'],
            ['BIMBEL SMA', 'Agus Setiawan', null, 'Tutor BIMBEL SMA.'],
        ];

        foreach ($tutors as [$programName, $name, $level, $notes]) {
            $programId = $programs[$programName] ?? null;

            if (!$programId) {
                continue;
            }

            $email = Str::slug($name, '.') . '@cell.local';

            DB::table('tutors')->updateOrInsert(
                [
                    'program_id' => $programId,
                    'name' => $name,
                    'level' => $level,
                ],
                [
                    'email' => $email,
                    'phone' => '081292538501',
                    'notes' => $notes,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        DB::table('schedule_templates')
            ->whereNull('tutor_id')
            ->orderBy('id')
            ->get(['id', 'program_id'])
            ->each(function ($template) {
                $tutorId = DB::table('tutors')
                    ->where('program_id', $template->program_id)
                    ->whereNull('level')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->value('id');

                if ($tutorId) {
                    DB::table('schedule_templates')
                        ->where('id', $template->id)
                        ->update([
                            'tutor_id' => $tutorId,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        $names = [
            'Siti Nur Aisyah',
            'Dina Maharani',
            'Rani Putri',
            'Aulia Rahman',
            'Nadia Fitri',
            'Rizky Pratama',
            'Maya Larasati',
            'Fajar Hidayat',
            'Kevin Saputra',
            'Laras Wulandari',
            'Arif Nugroho',
            'Mira Anggraini',
            'Dewi Kartika',
            'Gilang Mahendra',
            'Tania Safitri',
            'Bima Santoso',
            'Hana Prameswari',
            'Dimas Aditya',
            'Naufal Akbar',
            'Citra Amelia',
            'Indah Permata',
            'Yusuf Maulana',
            'Putri Ramadhani',
            'Agus Setiawan',
        ];

        DB::table('schedule_templates')
            ->whereIn('tutor_id', DB::table('tutors')->whereIn('name', $names)->pluck('id'))
            ->update(['tutor_id' => null]);

        DB::table('tutors')->whereIn('name', $names)->delete();
    }
};
