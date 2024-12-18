<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewPriceActionRequest extends FormRequest
{
    const ALLOWED_TRIGGERS = ['above', 'below'];

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

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
        $acceptedSymbols = config('bitfinex.symbols');

        return [
            'trigger' => 'required|in:'.implode(',', self::ALLOWED_TRIGGERS),
            'price' => 'required|numeric',
            'symbol' => 'required|string|in:'.implode(',', $acceptedSymbols),
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
