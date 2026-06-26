<?php

namespace Database\Seeders;

use App\Models\HomeClass;
use Illuminate\Database\Seeder;

class HomeClassSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->homeClasses() as $homeClass) {
            HomeClass::updateOrCreate(
                ['key' => $homeClass['key']],
                $homeClass
            );
        }
    }

    private function homeClasses(): array
    {
        return [
            [
                'key' => 'kids-teens',
                'title' => 'Kids & Teens',
                'badge' => null,
                'description' => 'Untuk membangun dasar Bahasa Inggris sejak usia sekolah dengan latihan yang menyenangkan.',
                'heading' => 'Kids',
                'heading_suffix' => ' & Teens',
                'quota_program_name' => 'English for Kids',
                'quota_label' => null,
                'features' => ['English for Kids', 'English for Teens', 'English for Adult', 'Opsi Reguler atau Private'],
                'modal_title' => 'Pilih Program Bahasa Inggris',
                'modal_description' => 'Pilih kelas sesuai usia dan kebutuhan siswa agar proses belajar lebih tepat, nyaman, dan mudah diikuti.',
                'modal_breadcrumbs' => ['Kelas', 'Bahasa Inggris', 'Kids & Teens'],
                'grid_columns' => 'lg:grid-cols-3',
                'sort_order' => 10,
                'is_featured' => false,
                'is_active' => true,
                'sub_programs' => [
                    [
                        'title' => 'English for Kids',
                        'program_name' => 'English for Kids',
                        'description' => 'Membangun fondasi Bahasa Inggris melalui aktivitas ringan, visual, dan menyenangkan.',
                        'badge' => 'Ages 6-12',
                        'icon' => 'child_care',
                        'features' => ['Vocabulary dasar', 'Fun activities', 'Confidence speaking'],
                    ],
                    [
                        'title' => 'English for Teens',
                        'program_name' => 'English for Teens',
                        'description' => 'Mempersiapkan remaja untuk komunikasi aktif, akademik, dan kepercayaan diri berbahasa.',
                        'badge' => 'Ages 13-18',
                        'icon' => 'groups',
                        'features' => ['Speaking practice', 'Grammar terarah', 'School support'],
                        'is_featured' => true,
                    ],
                    [
                        'title' => 'English for Adult',
                        'program_name' => 'English for Adult',
                        'description' => 'Penguasaan Bahasa Inggris untuk komunikasi kerja, studi, dan kebutuhan profesional.',
                        'badge' => 'Ages 18+',
                        'icon' => 'business_center',
                        'features' => ['Daily conversation', 'Professional English', 'Grammar for adults'],
                    ],
                ],
            ],
            [
                'key' => 'conversation-test',
                'title' => 'Conversation & Test Prep',
                'badge' => 'Program Bahasa Inggris',
                'description' => 'Untuk meningkatkan keberanian berbicara dan mempersiapkan kebutuhan TOEIC atau TOEFL.',
                'heading' => 'TOEIC',
                'heading_suffix' => ' / TOEFL',
                'quota_program_name' => 'English Conversation',
                'quota_label' => null,
                'features' => ['English Conversation', 'TOEIC', 'TOEFL'],
                'modal_title' => 'Pilih Program Test Preparation',
                'modal_description' => 'Pilih kelas untuk latihan percakapan, TOEIC, atau TOEFL sesuai target belajar siswa.',
                'modal_breadcrumbs' => ['Kelas', 'Bahasa Inggris', 'Conversation & Test Prep'],
                'grid_columns' => 'lg:grid-cols-3',
                'sort_order' => 20,
                'is_featured' => true,
                'is_active' => true,
                'sub_programs' => [
                    [
                        'title' => 'English Conversation',
                        'program_name' => 'English Conversation',
                        'description' => 'Latihan percakapan untuk membangun keberanian berbicara dan respons spontan.',
                        'badge' => 'Speaking',
                        'icon' => 'record_voice_over',
                        'features' => ['Daily speaking', 'Pronunciation', 'Confidence practice'],
                    ],
                    [
                        'title' => 'TOEIC',
                        'program_name' => 'TOEIC',
                        'description' => 'Persiapan TOEIC untuk kebutuhan kerja, sertifikasi, dan peningkatan kemampuan listening-reading.',
                        'badge' => 'Work Test',
                        'icon' => 'workspace_premium',
                        'features' => ['Listening strategy', 'Reading practice', 'Tryout pembahasan'],
                        'is_featured' => true,
                    ],
                    [
                        'title' => 'TOEFL',
                        'program_name' => 'TOEFL',
                        'description' => 'Persiapan TOEFL untuk kebutuhan akademik, studi lanjut, dan tes kemampuan Bahasa Inggris.',
                        'badge' => 'Academic Test',
                        'icon' => 'school',
                        'features' => ['Structure & grammar', 'Reading skill', 'Listening drill'],
                    ],
                ],
            ],
            [
                'key' => 'bimbel',
                'title' => 'BIMBEL Sekolah',
                'badge' => null,
                'description' => 'Untuk siswa TK sampai SMA yang membutuhkan pendampingan materi sekolah secara rutin.',
                'heading' => 'TK',
                'heading_suffix' => ' sampai SMA',
                'quota_program_name' => null,
                'quota_label' => 'Kuota per jenjang',
                'features' => ['Jenjang TK, SD, SMP, SMA', 'Pendalaman materi sekolah', 'Pendampingan belajar terarah'],
                'modal_title' => 'Pilih Jenjang BIMBEL',
                'modal_description' => 'Pilih jenjang bimbingan belajar sesuai kebutuhan siswa. Semua pilihan tetap masuk ke program BIMBEL yang diatur admin.',
                'modal_breadcrumbs' => ['Kelas', 'BIMBEL', 'TK sampai SMA'],
                'grid_columns' => 'md:grid-cols-2 xl:grid-cols-4',
                'sort_order' => 30,
                'is_featured' => false,
                'is_active' => true,
                'sub_programs' => [
                    ['title' => 'BIMBEL TK', 'program_name' => 'BIMBEL TK', 'description' => 'Pendampingan materi sekolah untuk jenjang TK dengan latihan terarah.', 'badge' => 'TK', 'icon' => 'child_care'],
                    ['title' => 'BIMBEL SD', 'program_name' => 'BIMBEL SD', 'description' => 'Pendampingan materi sekolah untuk jenjang SD dengan latihan terarah.', 'badge' => 'SD', 'icon' => 'menu_book'],
                    ['title' => 'BIMBEL SMP', 'program_name' => 'BIMBEL SMP', 'description' => 'Pendampingan materi sekolah untuk jenjang SMP dengan latihan terarah.', 'badge' => 'SMP', 'icon' => 'groups'],
                    ['title' => 'BIMBEL SMA', 'program_name' => 'BIMBEL SMA', 'description' => 'Pendampingan materi sekolah untuk jenjang SMA dengan latihan terarah.', 'badge' => 'SMA', 'icon' => 'school'],
                ],
            ],
        ];
    }
}
