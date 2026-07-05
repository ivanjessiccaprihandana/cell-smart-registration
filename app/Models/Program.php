<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'program_category_id',
        'name',
        'description',
        'category',
        'quota',
        'price',
        'private_price',
        'conversation_price',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'price' => 'integer',
        'private_price' => 'integer',
        'conversation_price' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get all users enrolled in this program
     */
    public function users(): HasMany
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function programCategory(): BelongsTo
    {
        return $this->belongsTo(ProgramCategory::class);
    }

    /**
     * Check if program is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               now()->between($this->start_date, $this->end_date);
    }

    public function registeredUsersCount(): int
    {
        $variant = $this->classTypeVariant();
        $baseProgram = $variant ? self::where('name', $variant['base_name'])->first() : null;

        $query = ProgramEnrollment::query()
            ->current()
            ->whereHas('user', function ($query) {
                $query->whereIn('payment_status', ['menunggu_verifikasi', 'diterima']);
            })
            ->where(function ($query) use ($variant, $baseProgram) {
                $query->where('program_id', $this->id);

                if ($variant && $baseProgram) {
                    $query->orWhere(function ($query) use ($baseProgram, $variant) {
                        $query
                            ->where('program_id', $baseProgram->id)
                            ->where('class_type', $variant['class_type']);
                    });
                }
            });

        return $query->distinct('user_id')->count('user_id');
    }

    public function remainingQuota(): ?int
    {
        if ($this->quota === null) {
            return null;
        }

        return max(0, $this->quota - $this->registeredUsersCount());
    }

    public function isFull(): bool
    {
        return $this->quota !== null && $this->remainingQuota() <= 0;
    }

    public function priceForClassType(?string $classType = null): ?int
    {
        return match ($classType) {
            'Private' => $this->private_price ?? $this->price,
            default => $this->price,
        };
    }

    public function formattedPriceForClassType(?string $classType = null, string $fallback = '-'): string
    {
        $price = $this->priceForClassType($classType);

        return $price !== null ? 'Rp ' . number_format($price, 0, ',', '.') : $fallback;
    }

    public function availableClassTypes(): array
    {
        $name = str($this->name)->lower()->toString();
        $category = str($this->category ?? '')->lower()->toString();

        if ($category === 'bimbel' || str_starts_with($name, 'bimbel')) {
            return ['Reguler'];
        }

        if ($name === 'english for adult') {
            return ['Reguler', 'Private'];
        }

        return ['Reguler'];
    }

    public function usesClassType(): bool
    {
        return count($this->availableClassTypes()) > 1 || $this->availableClassTypes() !== ['Reguler'];
    }

    public function allowsClassType(?string $classType): bool
    {
        return in_array($classType ?: 'Reguler', $this->availableClassTypes(), true);
    }

    public static function privatePackages(): array
    {
        return [
            'Conversation' => 'Conversation',
            'TOEFL Preparation' => 'TOEFL Preparation',
            'TOEIC Preparation' => 'TOEIC Preparation',
        ];
    }

    public function allowsPrivatePackage(?string $privatePackage): bool
    {
        return str($this->name)->lower()->toString() === 'english for adult'
            && in_array($privatePackage, array_keys(self::privatePackages()), true);
    }

    public function meetingCountForClassType(?string $classType = null, ?string $privatePackage = null): ?int
    {
        if (($classType ?: 'Reguler') === 'Private' && $this->allowsPrivatePackage($privatePackage)) {
            return 25;
        }

        return null;
    }

    public function classTypeVariant(): ?array
    {
        foreach (['Reguler', 'Private', 'Conversation'] as $classType) {
            $suffix = ' - ' . $classType;

            if (str_ends_with($this->name, $suffix)) {
                return [
                    'base_name' => str($this->name)->beforeLast($suffix)->toString(),
                    'class_type' => $classType,
                ];
            }
        }

        return null;
    }
}
