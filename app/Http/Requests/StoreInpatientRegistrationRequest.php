<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInpatientRegistrationRequest extends FormRequest
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
            'doctor_id' => 'required|exists:doctors,id',
            'bed_id' => 'required|exists:beds,id',
            'diagnosis' => 'required|string|max:500',
            'planned_admission_date' => 'nullable|date|after_or_equal:today',
            'estimated_discharge_date' => 'nullable|date|after:planned_admission_date',
            'payment_method' => 'required|in:cash,bpjs,insurance,company',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'patient_id' => 'patient',
            'doctor_id' => 'doctor',
            'bed_id' => 'bed',
            'diagnosis' => 'diagnosis',
            'planned_admission_date' => 'planned admission date',
            'estimated_discharge_date' => 'estimated discharge date',
            'payment_method' => 'payment method',
            'notes' => 'notes',
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
            'doctor_id.required' => 'Please select a doctor.',
            'doctor_id.exists' => 'The selected doctor is invalid.',
            'bed_id.required' => 'Please select a bed.',
            'bed_id.exists' => 'The selected bed is invalid.',
            'diagnosis.required' => 'Please enter the diagnosis.',
            'diagnosis.max' => 'The diagnosis must not exceed 500 characters.',
            'planned_admission_date.after_or_equal' => 'Planned admission date cannot be in the past.',
            'estimated_discharge_date.after' => 'Estimated discharge date must be after the admission date.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'notes.max' => 'Notes must not exceed 1000 characters.',
        ];
    }
}
