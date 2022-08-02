<?php

namespace App\Http\Requests\ServiceProvider;

use Illuminate\Foundation\Http\FormRequest;

class DeleteForAllRequest extends FormRequest
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
     * @inheritDoc
     */
    protected function prepareForValidation()
    {
//        ids,start,end added in javascript and don,t exists $this->>request and adding & overriding its to request and data to prepare set rule
        $this->request->add(['ids' => explode(',', $this->ids)]);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:attachments,id'],
//            'path' => ['exists:attachments,path']
        ];
    }
}
