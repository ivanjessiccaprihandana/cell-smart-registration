<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::create([
            'name' => 'Web Development Basics',
            'description' => 'Learn the fundamentals of web development including HTML, CSS, and JavaScript.',
            'category' => 'Technology',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addMonths(2),
            'status' => 'active',
        ]);

        Program::create([
            'name' => 'Advanced Laravel',
            'description' => 'Deep dive into Laravel framework with advanced patterns and best practices.',
            'category' => 'Technology',
            'start_date' => now()->addWeeks(2),
            'end_date' => now()->addMonths(3),
            'status' => 'active',
        ]);

        Program::create([
            'name' => 'Digital Marketing',
            'description' => 'Master digital marketing strategies and tools.',
            'category' => 'Marketing',
            'start_date' => now()->addMonth(),
            'end_date' => now()->addMonths(4),
            'status' => 'active',
        ]);

        // Create additional programs using factory
        Program::factory(5)->create();
    }
}
