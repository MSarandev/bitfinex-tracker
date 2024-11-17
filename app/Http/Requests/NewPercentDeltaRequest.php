<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewPercentDeltaRequest extends FormRequest
{
    // private const TIMEFRAME_FLAGS = ["H", "D", "W", "M", "Y"];

    // This is restricted as it's a demo app. Request chunking will be needed otherwise, as the API limits to 250 items
    private const TIMEFRAME_FLAGS = ["H"];

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
            // This is restricted as it's a demo app, remove on prod (1H to 23H)
            'timeframe_value' => 'required|numeric|min:1|max:23',
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
            'timeframe_flags'.
            '.in' => '[RESTRICTED - DEMO] '.
                'The timeframe flag must be one of: '.implode(', ', self::TIMEFRAME_FLAGS),
        ];
    }
}
