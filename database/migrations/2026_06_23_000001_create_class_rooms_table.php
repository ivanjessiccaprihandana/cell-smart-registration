<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category');
            $table->unsignedInteger('capacity')->default(8);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        $now = now();
        $rooms = [
            ['name' => 'English Room 1', 'category' => 'English', 'capacity' => 8],
            ['name' => 'English Room 2', 'category' => 'English', 'capacity' => 8],
            ['name' => 'English Room 3', 'category' => 'English', 'capacity' => 8],
            ['name' => 'Bimbel Room 1', 'category' => 'Bimbel', 'capacity' => 8],
            ['name' => 'Bimbel Room 2', 'category' => 'Bimbel', 'capacity' => 8],
            ['name' => 'Bimbel Room 3', 'category' => 'Bimbel', 'capacity' => 8],
        ];

        foreach ($rooms as $room) {
            DB::table('class_rooms')->insert([
                ...$room,
                'is_active' => true,
                'notes' => 'Data ruang kelas CELL.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
