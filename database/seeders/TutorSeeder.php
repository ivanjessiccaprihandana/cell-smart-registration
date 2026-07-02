<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Tutor;
use Illuminate\Database\Seeder;

class TutorSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::query()->pluck('id', 'name');

        $tutors = [
            ['English for Kids', 'Siti Nur Aisyah', 'siti.aisyah@cell.local', '081292538501', 'Tutor kelas anak dan fondasi bahasa Inggris.'],
            ['English for Teens', 'Rani Putri', 'rani.putri@cell.local', '081292538502', 'Tutor kelas remaja dan speaking confidence.'],
            ['English for Adult', 'Mira Anggraini', 'mira.anggraini@cell.local', '081292538503', 'Tutor English for Adult reguler dan private.'],
            ['English for Adult', 'Laras Wulandari', 'laras.wulandari@cell.local', '081292538504', 'Tutor private Adult paket Conversation.'],
            ['English for Adult', 'Dewi Lestari', 'dewi.lestari@cell.local', '081292538505', 'Tutor private Adult paket TOEFL Preparation.'],
            ['English for Adult', 'Fajar Prakoso', 'fajar.prakoso@cell.local', '081292538506', 'Tutor private Adult paket TOEIC Preparation.'],
            ['BIMBEL TK', 'Nia Kartika', 'nia.kartika@cell.local', '081292538507', 'Tutor pendamping belajar jenjang TK.'],
            ['BIMBEL SD', 'Agus Setiawan', 'agus.setiawan@cell.local', '081292538508', 'Tutor pendamping belajar jenjang SD.'],
            ['BIMBEL SMP', 'Dwi Prasetyo', 'dwi.prasetyo@cell.local', '081292538509', 'Tutor pendamping belajar jenjang SMP.'],
            ['BIMBEL SMA', 'Ratna Sari', 'ratna.sari@cell.local', '081292538510', 'Tutor pendamping belajar jenjang SMA.'],
        ];

        foreach ($tutors as [$programName, $name, $email, $phone, $notes]) {
            $programId = $programs[$programName] ?? null;

            if (!$programId) {
                continue;
            }

            Tutor::query()->updateOrCreate(
                ['email' => $email],
                [
                    'program_id' => $programId,
                    'name' => $name,
                    'phone' => $phone,
                    'level' => null,
                    'notes' => $notes,
                    'is_active' => true,
                ]
            );
        }
    }
}
