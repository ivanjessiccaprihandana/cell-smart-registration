<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Add validation rules for joining program
            // For example:
            // 'certificate' => 'required|mimes:pdf',
            // 'experience' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            // Add custom messages if needed
        ];
    }
}
