<?php

namespace App\Http\Requests;

use App\Models\Ward;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWardRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:10', 'unique:wards'],
            'name' => ['required', 'string', 'max:100'],
            'class' => ['required', 'in:' . implode(',', [Ward::CLASS_VIP, Ward::CLASS_CLASS_1, Ward::CLASS_CLASS_2, Ward::CLASS_CLASS_3])],
            'floor' => ['nullable', 'integer', 'min:1', 'max:20'],
            'building' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'code' => 'ward code',
            'name' => 'ward name',
            'class' => 'ward class',
            'floor' => 'floor number',
            'building' => 'building name',
        ];
    }
}
