<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'medical_record_number' => ['nullable', 'string', 'max:50', 'unique:patients'],
            'nik' => ['required', 'string', 'size:16', 'unique:patients'],
            'name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:' . implode(',', [Patient::GENDER_MALE, Patient::GENDER_FEMALE])],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'blood_type' => ['nullable', 'in:' . implode(',', [Patient::BLOOD_TYPE_A, Patient::BLOOD_TYPE_B, Patient::BLOOD_TYPE_AB, Patient::BLOOD_TYPE_O, Patient::BLOOD_TYPE_UNKNOWN])],
            'marital_status' => ['nullable', 'in:' . implode(',', [Patient::MARITAL_SINGLE, Patient::MARITAL_MARRIED, Patient::MARITAL_DIVORCED, Patient::MARITAL_WIDOWED])],
            'religion' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'medical_record_number' => 'medical record number',
            'nik' => 'NIK',
            'name' => 'patient name',
            'birth_date' => 'birth date',
            'birth_place' => 'birth place',
            'gender' => 'gender',
            'address' => 'address',
            'phone' => 'phone number',
            'blood_type' => 'blood type',
            'marital_status' => 'marital status',
            'religion' => 'religion',
            'occupation' => 'occupation',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_phone' => 'emergency contact phone',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nik.size' => 'NIK must be exactly 16 digits.',
            'birth_date.before' => 'Birth date must be before today.',
        ];
    }
}
