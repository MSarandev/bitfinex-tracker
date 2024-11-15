<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewPercentDeltaRequest extends FormRequest
{
    private const TIMEFRAME_FLAGS = ["D", "W", "M", "Y"];

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
            'timeframe_flags' => 'required|in:'.implode(',', self::TIMEFRAME_FLAGS),
            'timeframe_value' => 'required|numeric|min:1',
            'percent_change' => 'required|numeric|min:0',
            'symbol' => 'required|string|in:'.implode(',', $acceptedSymbols),
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'trigger.in' => 'The timeframe flag must be one of: '.implode(', ', self::TIMEFRAME_FLAGS),
        ];
    }
}
