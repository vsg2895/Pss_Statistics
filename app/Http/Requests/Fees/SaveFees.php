<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class SaveFees extends FormRequest
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
            "values.*" => ['nullable', 'numeric','gt:0'],
            "values.5" => ['nullable', 'numeric'],
            "values.9" => ['nullable', 'numeric'],
            "values.10" => ['nullable', 'numeric'],
            "values.11" => ['nullable', 'numeric'],
        ];
    }
}
