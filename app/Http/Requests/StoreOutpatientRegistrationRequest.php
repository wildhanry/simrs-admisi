<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutpatientRegistrationRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'polyclinic_id' => 'required|exists:polyclinics,id',
            'doctor_id' => 'required|exists:doctors,id',
            'complaint' => 'required|string|max:500',
            'payment_method' => 'required|in:cash,bpjs,insurance,company',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'patient_id' => 'patient',
            'polyclinic_id' => 'polyclinic',
            'doctor_id' => 'doctor',
            'complaint' => 'chief complaint',
            'payment_method' => 'payment method',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'The selected patient is invalid.',
            'polyclinic_id.required' => 'Please select a polyclinic.',
            'polyclinic_id.exists' => 'The selected polyclinic is invalid.',
            'doctor_id.required' => 'Please select a doctor.',
            'doctor_id.exists' => 'The selected doctor is invalid.',
            'complaint.required' => 'Please describe the chief complaint.',
            'complaint.max' => 'The chief complaint must not exceed 500 characters.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
        ];
    }
}
