<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewPriceActionRequest extends FormRequest
{
    const ALLOWED_TRIGGERS = ['above', 'below'];

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'trigger' => 'required|in:'.implode(',', self::ALLOWED_TRIGGERS),
            'price' => 'required|numeric',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'trigger.in' => 'The trigger field must be one of: '.implode(', ', self::ALLOWED_TRIGGERS),
        ];
    }
}
