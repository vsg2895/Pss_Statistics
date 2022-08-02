<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanySave extends FormRequest
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
            'tags' => ['array', 'sometimes', 'required'],
            'tags.*' => ['integer', 'sometimes', 'required', 'exists:tags,id'],
            'service_provider_id' => ['nullable', 'integer', 'exists:service_providers,id'],
        ];
    }
}
