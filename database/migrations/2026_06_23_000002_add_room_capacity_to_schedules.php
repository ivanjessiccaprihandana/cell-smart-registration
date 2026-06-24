<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_templates', function (Blueprint $table) {
            $table->foreignId('class_room_id')->nullable()->after('tutor_id')->constrained('class_rooms')->nullOnDelete();
            $table->unsignedInteger('max_students')->default(8)->after('room');
        });

        Schema::table('class_schedules', function (Blueprint $table) {
            $table->foreignId('class_room_id')->nullable()->after('schedule_template_id')->constrained('class_rooms')->nullOnDelete();
            $table->unsignedInteger('max_students')->nullable()->after('room');
        });

        $englishRoom = DB::table('class_rooms')->where('category', 'English')->orderBy('id')->first();
        $bimbelRoom = DB::table('class_rooms')->where('category', 'Bimbel')->orderBy('id')->first();

        $programs = DB::table('programs')->get(['id', 'name', 'category']);

        foreach ($programs as $program) {
            $isBimbel = strtolower((string) $program->category) === 'bimbel'
                || str_starts_with(strtolower($program->name), 'bimbel');
            $room = $isBimbel ? $bimbelRoom : $englishRoom;

            DB::table('schedule_templates')
                ->where('program_id', $program->id)
                ->whereNull('class_room_id')
                ->update([
                    'class_room_id' => $room?->id,
                    'room' => $room?->name,
                ]);
        }

        DB::table('schedule_templates')
            ->where('class_type', 'Private')
            ->update(['max_students' => 1]);
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_room_id');
            $table->dropColumn('max_students');
        });

        Schema::table('schedule_templates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_room_id');
            $table->dropColumn('max_students');
        });
    }
};
