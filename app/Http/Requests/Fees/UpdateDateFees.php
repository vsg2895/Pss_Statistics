<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDateFees extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "fee_type_ids" => ['required', 'array'],
            "fee_type_ids.*" => ['required', 'integer', 'exists:fee_types,id'],
            "values" => ['required', 'array'],
            "values.*" => ['numeric'],
            'start' => ['nullable', 'date', 'required_with:end,checks', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['nullable', 'date', 'required_with:start,checks', 'date_format:Y-m-d', 'after_or_equal:start'],
            'checks' => ['nullable', 'array'],
            "checks.*" => ['numeric'],
        ];
    }
}
