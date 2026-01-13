<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Gender constants
     */
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * Blood type constants
     */
    const BLOOD_TYPE_A = 'A';
    const BLOOD_TYPE_B = 'B';
    const BLOOD_TYPE_AB = 'AB';
    const BLOOD_TYPE_O = 'O';
    const BLOOD_TYPE_UNKNOWN = 'unknown';

    /**
     * Marital status constants
     */
    const MARITAL_SINGLE = 'single';
    const MARITAL_MARRIED = 'married';
    const MARITAL_DIVORCED = 'divorced';
    const MARITAL_WIDOWED = 'widowed';

    protected $fillable = [
        'medical_record_number',
        'nik',
        'name',
        'birth_date',
        'birth_place',
        'gender',
        'address',
        'phone',
        'blood_type',
        'marital_status',
        'religion',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Get all registrations for this patient
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get latest registration
     */
    public function latestRegistration()
    {
        return $this->hasOne(Registration::class)->latestOfMany();
    }

    /**
     * Get outpatient registrations
     */
    public function outpatientRegistrations()
    {
        return $this->hasMany(Registration::class)->where('type', 'outpatient');
    }

    /**
     * Get inpatient registrations
     */
    public function inpatientRegistrations()
    {
        return $this->hasMany(Registration::class)->where('type', 'inpatient');
    }

    /**
     * Get patient's age
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date->diffInYears(now());
    }

    /**
     * Get gender label
     */
    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            self::GENDER_MALE => 'Laki-laki',
            self::GENDER_FEMALE => 'Perempuan',
            default => 'Unknown',
        };
    }

    /**
     * Get marital status label
     */
    public function getMaritalStatusLabelAttribute(): ?string
    {
        return match($this->marital_status) {
            self::MARITAL_SINGLE => 'Belum Menikah',
            self::MARITAL_MARRIED => 'Menikah',
            self::MARITAL_DIVORCED => 'Cerai',
            self::MARITAL_WIDOWED => 'Janda/Duda',
            default => null,
        };
    }

    /**
     * Scope for searching patients
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('medical_record_number', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
