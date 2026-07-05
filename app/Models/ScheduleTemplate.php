<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleTemplate extends Model
{
    protected $fillable = [
        'program_id',
        'tutor_id',
        'class_room_id',
        'class_type',
        'private_package',
        'level',
        'batch_name',
        'registration_start_date',
        'registration_end_date',
        'learning_start_date',
        'learning_end_date',
        'days',
        'start_time',
        'end_time',
        'room',
        'max_students',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'days' => 'array',
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'learning_start_date' => 'date',
        'learning_end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'max_students' => 'integer',
        'is_active' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(SchedulePreference::class);
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function activeStudentCount(): int
    {
        return $this->preferences()
            ->whereIn('status', ['pending', 'assigned'])
            ->whereHas('user', function ($query) {
                $query
                    ->where('program', (string) $this->program_id)
                    ->whereIn('payment_status', ['menunggu_verifikasi', 'diterima'])
                    ->whereHas('programEnrollments', function ($query) {
                        $query
                            ->current()
                            ->where('program_id', $this->program_id);

                        if ($this->class_type) {
                            $query->where('class_type', $this->class_type);
                        }

                        if ($this->private_package) {
                            $query->where('private_package', $this->private_package);
                        } else {
                            $query->whereNull('private_package');
                        }
                    });

                if ($this->class_type) {
                    $query->where('class_type', $this->class_type);
                }

                if ($this->private_package) {
                    $query->where('private_package', $this->private_package);
                } else {
                    $query->whereNull('private_package');
                }
            })
            ->distinct('user_id')
            ->count('user_id');
    }

    public function remainingSeats(): int
    {
        return max(0, (int) $this->max_students - $this->activeStudentCount());
    }

    public function isFull(): bool
    {
        return $this->remainingSeats() <= 0;
    }

    public function hasSeatForUser(int $userId): bool
    {
        $usedSeats = $this->preferences()
            ->whereIn('status', ['pending', 'assigned'])
            ->where('user_id', '!=', $userId)
            ->whereHas('user', function ($query) {
                $query
                    ->where('program', (string) $this->program_id)
                    ->whereIn('payment_status', ['menunggu_verifikasi', 'diterima'])
                    ->whereHas('programEnrollments', function ($query) {
                        $query
                            ->current()
                            ->where('program_id', $this->program_id);

                        if ($this->class_type) {
                            $query->where('class_type', $this->class_type);
                        }

                        if ($this->private_package) {
                            $query->where('private_package', $this->private_package);
                        } else {
                            $query->whereNull('private_package');
                        }
                    });

                if ($this->class_type) {
                    $query->where('class_type', $this->class_type);
                }

                if ($this->private_package) {
                    $query->where('private_package', $this->private_package);
                } else {
                    $query->whereNull('private_package');
                }
            })
            ->distinct('user_id')
            ->count('user_id');

        return $usedSeats < (int) $this->max_students;
    }
}
