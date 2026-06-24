<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_templates', function (Blueprint $table) {
            $table->string('batch_name')->nullable()->after('level');
            $table->date('registration_start_date')->nullable()->after('batch_name');
            $table->date('registration_end_date')->nullable()->after('registration_start_date');
            $table->date('learning_start_date')->nullable()->after('registration_end_date');
            $table->date('learning_end_date')->nullable()->after('learning_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_templates', function (Blueprint $table) {
            $table->dropColumn([
                'batch_name',
                'registration_start_date',
                'registration_end_date',
                'learning_start_date',
                'learning_end_date',
            ]);
        });
    }
};
