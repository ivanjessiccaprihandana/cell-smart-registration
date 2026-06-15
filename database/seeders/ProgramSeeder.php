<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $english = $this->category('Bahasa Inggris', null, 10, 'Program Bahasa Inggris CELL English Course.');
        $testPrep = $this->category('Test Preparation', null, 20, 'Persiapan TOEIC dan TOEFL.');
        $bimbel = $this->category('BIMBEL', null, 30, 'Bimbingan belajar sekolah.');

        $levels = [
            'English for Kids' => [
                'sort' => 10,
                'description' => 'Program Bahasa Inggris untuk anak usia 6-12 tahun.',
                'quota' => 20,
                'price' => 750000,
                'private_price' => 1200000,
                'conversation_price' => 950000,
            ],
            'English for Teens' => [
                'sort' => 20,
                'description' => 'Program Bahasa Inggris untuk remaja usia 13-18 tahun.',
                'quota' => 18,
                'price' => 850000,
                'private_price' => 1350000,
                'conversation_price' => 1100000,
            ],
            'English for Adult' => [
                'sort' => 30,
                'description' => 'Program Bahasa Inggris untuk dewasa dan profesional.',
                'quota' => 15,
                'price' => 950000,
                'private_price' => 1500000,
                'conversation_price' => 1250000,
            ],
        ];

        foreach ($levels as $levelName => $level) {
            $levelCategory = $this->category($levelName, $english->id, $level['sort'], $level['description']);

            Program::updateOrCreate(
                ['name' => $levelName],
                [
                    'program_category_id' => $levelCategory->id,
                    'description' => $level['description'],
                    'category' => 'Bahasa Inggris',
                    'quota' => $level['quota'],
                    'price' => $level['price'],
                    'private_price' => $level['private_price'],
                    'conversation_price' => $level['conversation_price'],
                    'start_date' => now(),
                    'end_date' => now()->addMonths(6),
                    'status' => 'active',
                ]
            );
        }

        foreach ([
            ['English Conversation', $english->id, 'Bahasa Inggris', 16, 1200000, 'Latihan percakapan agar siswa lebih percaya diri berbicara Bahasa Inggris.'],
            ['TOEIC', $testPrep->id, 'Test Preparation', 12, 2499000, 'Persiapan TOEIC dengan latihan soal dan strategi pengerjaan.'],
            ['TOEFL', $testPrep->id, 'Test Preparation', 12, 2499000, 'Persiapan TOEFL untuk kebutuhan akademik dan tes kemampuan Bahasa Inggris.'],
            ['BIMBEL TK', $bimbel->id, 'BIMBEL', 10, 500000, 'Pendampingan belajar sekolah untuk jenjang TK.'],
            ['BIMBEL SD', $bimbel->id, 'BIMBEL', 15, 600000, 'Pendampingan belajar sekolah untuk jenjang SD.'],
            ['BIMBEL SMP', $bimbel->id, 'BIMBEL', 15, 700000, 'Pendampingan belajar sekolah untuk jenjang SMP.'],
            ['BIMBEL SMA', $bimbel->id, 'BIMBEL', 15, 800000, 'Pendampingan belajar sekolah untuk jenjang SMA.'],
        ] as [$name, $categoryId, $category, $quota, $price, $description]) {
            Program::updateOrCreate(
                ['name' => $name],
                [
                    'program_category_id' => $categoryId,
                    'description' => $description,
                    'category' => $category,
                    'quota' => $quota,
                    'price' => $price,
                    'private_price' => null,
                    'conversation_price' => null,
                    'start_date' => now(),
                    'end_date' => now()->addMonths(6),
                    'status' => 'active',
                ]
            );
        }
    }

    private function category(string $name, ?int $parentId, int $sortOrder, ?string $description = null): ProgramCategory
    {
        return ProgramCategory::updateOrCreate(
            [
                'parent_id' => $parentId,
                'slug' => Str::slug(($parentId ? $parentId . '-' : '') . $name),
            ],
            [
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]
        );
    }
}
