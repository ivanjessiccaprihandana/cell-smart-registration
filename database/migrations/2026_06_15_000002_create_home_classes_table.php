<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_classes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->string('heading');
            $table->string('heading_suffix')->nullable();
            $table->string('quota_program_name')->nullable();
            $table->string('quota_label')->nullable();
            $table->json('features')->nullable();
            $table->string('modal_title');
            $table->text('modal_description')->nullable();
            $table->json('modal_breadcrumbs')->nullable();
            $table->json('sub_programs')->nullable();
            $table->string('grid_columns')->default('lg:grid-cols-3');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_classes');
    }
};
