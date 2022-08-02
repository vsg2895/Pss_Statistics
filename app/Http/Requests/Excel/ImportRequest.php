<?php

namespace App\Http\Requests\Excel;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
            'import' => ['required', 'mimes:xlsx,csv,xls'],
            'start' => ['nullable', 'date', 'required_with:end,checks', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['nullable', 'date', 'required_with:start,checks', 'date_format:Y-m-d', 'after_or_equal:start'],
        ];
    }
}
