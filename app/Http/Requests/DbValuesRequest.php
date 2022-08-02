<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DbValuesRequest extends FormRequest
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
            'start' => ['date', 'required_with:end', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['date', 'required_with:start', 'date_format:Y-m-d', 'after_or_equal:start'],
        ];
    }
}
