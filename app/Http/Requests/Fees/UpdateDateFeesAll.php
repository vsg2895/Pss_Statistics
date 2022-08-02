<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDateFeesAll extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * @inheritDoc
     */
    protected function prepareForValidation()
    {
//        ids,start,end added in javascript and don,t exists $this->>request and adding & overriding its to request and data to prepare set rule
        $this->request->add(['ids' => array_map('intval', explode(',', $this->ids))]);
        $this->request->add(['start' => $this->start]);
        $this->request->add(['end' => $this->end]);
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
            "values.*" => ['nullable', 'numeric'],
            'start' => ['date', 'required_with:end,checks', 'date_format:Y-m-d', 'before_or_equal:end'],
            'end' => ['date', 'required_with:start,checks', 'date_format:Y-m-d', 'after_or_equal:start'],
            'checks' => ['required', 'array'],
            "checks.*" => ['required', 'numeric'],
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:companies,company_id'],
        ];
    }
}
