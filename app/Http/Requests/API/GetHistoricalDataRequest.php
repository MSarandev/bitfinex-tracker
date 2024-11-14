<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetHistoricalDataRequest extends FormRequest
{
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
        return [
            'symbol' => 'required|string',
            'from' => 'date',
            'to' => 'date',
        ];
    }

    public function messages(): array
    {
        return [
            'from.date' => 'Datetime should comply with the ISO-8601 format -> '.DATE_ATOM,
            'to.date' => 'Datetime should comply with the ISO-8601 format -> '.DATE_ATOM,
        ];
    }
}
