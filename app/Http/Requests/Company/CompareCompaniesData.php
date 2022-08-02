<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompareCompaniesData extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start'],
            's_start' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:s_end'],
            's_end' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:s_start'],
//            not negative number
            'calls_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
