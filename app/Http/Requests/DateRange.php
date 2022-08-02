<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DateRange extends FormRequest
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
            'start' => ['sometimes', 'nullable', 'date', 'required_with:end,date_range', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['sometimes', 'nullable', 'date', 'required_with:start,date_range', 'date_format:Y-m-d', 'after_or_equal:start'],
            'tags' => ['nullable', 'array'],
            'page' => ['nullable', 'integer'],
            'tags.*' => ['required', 'string', 'exists:tags,id', 'sometimes'],
            'provider' => ['nullable', 'boolean'],
        ];
    }
}
