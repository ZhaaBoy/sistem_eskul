<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('parents')) {
            Schema::create('parents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('relationship')->default('Wali');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('coaches')) {
            Schema::create('coaches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('nip')->nullable()->unique();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
                $table->string('nis')->nullable()->unique();
                $table->string('nisn')->nullable()->unique();
                $table->string('name');
                $table->string('class')->nullable();
                $table->string('major')->nullable();
                $table->string('gender')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('extracurriculars')) {
            Schema::create('extracurriculars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coach_id')->constrained('coaches')->restrictOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedInteger('quota')->nullable();
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('extracurricular_schedules')) {
            Schema::create('extracurricular_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
                $table->string('day');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('location')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('extracurricular_registrations')) {
            Schema::create('extracurricular_registrations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
                $table->string('status')->default('menunggu_validasi_orang_tua');
                $table->timestamp('parent_approved_at')->nullable();
                $table->timestamp('parent_rejected_at')->nullable();
                $table->text('parent_rejection_reason')->nullable();
                $table->timestamp('coach_approved_at')->nullable();
                $table->timestamp('coach_rejected_at')->nullable();
                $table->text('coach_rejection_reason')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'extracurricular_id'], 'registrations_student_extracurricular_unique');
            });
        }

        if (! Schema::hasTable('extracurricular_members')) {
            Schema::create('extracurricular_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
                $table->foreignId('registration_id')->nullable()->constrained('extracurricular_registrations')->nullOnDelete();
                $table->timestamp('joined_at')->nullable();
                $table->string('status')->default('aktif');
                $table->timestamps();

                $table->unique(['student_id', 'extracurricular_id'], 'members_student_extracurricular_unique');
            });
        }

        if (! Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
                $table->foreignId('schedule_id')->constrained('extracurricular_schedules')->cascadeOnDelete();
                $table->date('attendance_date');
                $table->string('status')->default('menunggu_approval');
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'schedule_id', 'attendance_date'], 'attendance_student_schedule_date_unique');
            });
        }

        if (! Schema::hasTable('assessments')) {
            Schema::create('assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('extracurricular_id')->constrained()->cascadeOnDelete();
                $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete();
                $table->string('period')->nullable();
                $table->string('semester');
                $table->decimal('score', 5, 2);
                $table->string('predicate')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'extracurricular_id', 'period', 'semester'], 'assessment_student_extracurricular_period_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('extracurricular_members');
        Schema::dropIfExists('extracurricular_registrations');
        Schema::dropIfExists('extracurricular_schedules');
        Schema::dropIfExists('extracurriculars');
        Schema::dropIfExists('students');
        Schema::dropIfExists('coaches');
        Schema::dropIfExists('parents');
    }
};
