<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DataFilter extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
        $rule = Rule::requiredIf(function () {
            return request()->start;
        });
        return [//todo don't let start_date and date_range together
            'start_date' => ['sometimes', 'date', 'required_with:compare_date', 'date_format:Y-m-d', 'after_or_equal:compare_date'],
            'compare_date' => ['sometimes', 'date', 'required_with:start_date', 'date_format:Y-m-d', 'before_or_equal:start_date'],
            'date_range' => ['sometimes', 'required_:start,end', 'in:true'],
            'start' => ['sometimes', 'date', 'required_with:end,date_range', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['sometimes', 'date', 'required_with:start,date_range', 'date_format:Y-m-d', 'after_or_equal:start'],
        ];
    }
}
