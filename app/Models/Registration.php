<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Type constants
     */
    const TYPE_OUTPATIENT = 'outpatient';
    const TYPE_INPATIENT = 'inpatient';

    /**
     * Status constants
     */
    const STATUS_WAITING = 'waiting';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment method constants
     */
    const PAYMENT_BPJS = 'BPJS';
    const PAYMENT_CASH = 'cash';
    const PAYMENT_INSURANCE = 'insurance';

    protected $fillable = [
        'registration_number',
        'patient_id',
        'doctor_id',
        'user_id',
        'type',
        'polyclinic_id',
        'bed_id',
        'admission_date',
        'discharge_date',
        'registration_date',
        'registration_time',
        'payment_method',
        'complaint',
        'diagnosis',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'admission_date' => 'datetime',
            'discharge_date' => 'datetime',
            'registration_date' => 'date',
        ];
    }

    /**
     * Get the patient
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the user who created this registration
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the polyclinic (for outpatient)
     */
    public function polyclinic()
    {
        return $this->belongsTo(Polyclinic::class);
    }

    /**
     * Get the bed (for inpatient)
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get the ward through bed (for inpatient)
     */
    public function ward()
    {
        return $this->hasOneThrough(Ward::class, Bed::class, 'id', 'id', 'bed_id', 'ward_id');
    }

    /**
     * Scope for outpatient registrations
     */
    public function scopeOutpatient($query)
    {
        return $query->where('type', 'outpatient');
    }

    /**
     * Scope for inpatient registrations
     */
    public function scopeInpatient($query)
    {
        return $query->where('type', 'inpatient');
    }

    /**
     * Scope for today's registrations
     */
    public function scopeToday($query)
    {
        return $query->whereDate('registration_date', now()->toDateString());
    }

    /**
     * Scope for active registrations (waiting or in progress)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_WAITING, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Scope for completed registrations
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for cancelled registrations
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('registration_date', [$startDate, $endDate]);
    }

    /**
     * Scope for payment method
     */
    public function scopePaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for active inpatients (not discharged)
     */
    public function scopeActiveInpatients($query)
    {
        return $query->where('type', self::TYPE_INPATIENT)
            ->whereIn('status', [self::STATUS_WAITING, self::STATUS_IN_PROGRESS])
            ->whereNull('discharge_date');
    }

    /**
     * Scope for searching registrations
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('registration_number', 'like', "%{$search}%")
                ->orWhereHas('patient', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Check if registration is outpatient
     */
    public function isOutpatient(): bool
    {
        return $this->type === 'outpatient';
    }

    /**
     * Check if registration is inpatient
     */
    public function isInpatient(): bool
    {
        return $this->type === self::TYPE_INPATIENT;
    }

    /**
     * Check if registration is active
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_WAITING, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Check if registration is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_WAITING => 'Menunggu',
            self::STATUS_IN_PROGRESS => 'Sedang Dilayani',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_OUTPATIENT => 'Rawat Jalan',
            self::TYPE_INPATIENT => 'Rawat Inap',
            default => 'Unknown',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            self::PAYMENT_BPJS => 'BPJS Kesehatan',
            self::PAYMENT_CASH => 'Tunai',
            self::PAYMENT_INSURANCE => 'Asuransi',
            default => 'Unknown',
        };
    }

    /**
     * Generate registration number
     */
    public static function generateRegistrationNumber(string $type): string
    {
        $prefix = $type === self::TYPE_OUTPATIENT ? 'RJ' : 'RI';
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now()->toDateString())
            ->where('type', $type)
            ->count() + 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate registration number on creating
        static::creating(function ($registration) {
            if (empty($registration->registration_number)) {
                $registration->registration_number = static::generateRegistrationNumber($registration->type);
            }
        });
    }
}
