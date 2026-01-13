<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'specialization' => ['required', 'string', 'max:50'],
            'sip_number' => ['required', 'string', 'max:50', 'unique:doctors'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'availability' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'doctor name',
            'specialization' => 'specialization',
            'sip_number' => 'SIP number',
            'phone' => 'phone number',
            'email' => 'email address',
            'availability' => 'availability schedule',
            'is_active' => 'active status',
        ];
    }
}
