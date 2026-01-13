<?php

namespace App\Http\Requests;

use App\Models\Bed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBedRequest extends FormRequest
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
            'ward_id' => ['required', 'exists:wards,id'],
            'bed_number' => ['required', 'string', 'max:20'],
            'status' => ['required', 'in:' . implode(',', [Bed::STATUS_AVAILABLE, Bed::STATUS_OCCUPIED, Bed::STATUS_MAINTENANCE, Bed::STATUS_RESERVED])],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ward_id' => 'ward',
            'bed_number' => 'bed number',
            'status' => 'bed status',
        ];
    }
}
