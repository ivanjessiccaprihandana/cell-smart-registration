<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('private_package')->nullable()->after('class_type');
        });

        Schema::table('program_enrollments', function (Blueprint $table) {
            $table->string('private_package')->nullable()->after('class_type');
        });

        Schema::table('schedule_templates', function (Blueprint $table) {
            $table->string('private_package')->nullable()->after('class_type');
        });

        Schema::table('class_schedules', function (Blueprint $table) {
            $table->string('private_package')->nullable()->after('class_type');
        });
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn('private_package');
        });

        Schema::table('schedule_templates', function (Blueprint $table) {
            $table->dropColumn('private_package');
        });

        Schema::table('program_enrollments', function (Blueprint $table) {
            $table->dropColumn('private_package');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('private_package');
        });
    }
};
