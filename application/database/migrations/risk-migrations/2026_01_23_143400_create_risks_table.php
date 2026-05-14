<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assessment_id')
                ->unique()
                ->constrained('assessments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('percentage', 5, 2);
            $table->enum('classification', ['low', 'moderate', 'high']);
            $table->unsignedInteger('score');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
