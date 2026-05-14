<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')
                ->constrained('patients')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('ubs_id')
                ->constrained('ubs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->text('symptoms');
            $table->jsonb('answers');
            $table->timestamps();

            $table->index('patient_id');
            $table->index('user_id');
            $table->index('ubs_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
