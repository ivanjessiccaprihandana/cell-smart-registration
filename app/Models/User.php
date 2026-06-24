<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'whatsapp', 'address', 'program', 'class_type', 'payment_proof_path', 'payment_status', 'registration_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'registration_expires_at' => 'datetime',
        ];
    }

    /**
     * Get all program enrollments for this user
     */
    public function programEnrollments(): HasMany
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function schedulePreferences(): HasMany
    {
        return $this->hasMany(SchedulePreference::class);
    }

    public function placementTestAttempts(): HasMany
    {
        return $this->hasMany(PlacementTestAttempt::class);
    }

    public function latestPlacementAttempt(): HasOne
    {
        return $this->hasOne(PlacementTestAttempt::class)->latestOfMany();
    }

    /**
     * Get all programs this user is enrolled in
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_enrollments')
                    ->withTimestamps()
                    ->withPivot('enrolled_at', 'status');
    }

    /**
     * Get active program enrollments
     */
    public function activePrograms()
    {
        return $this->programs()->wherePivot('status', 'active');
    }
}
