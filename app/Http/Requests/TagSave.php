<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagSave extends FormRequest
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
        $id = $this->route('tag') ? $this->route('tag')->id : null;
        return [
            'name'  => ['required', 'string', 'max:255', 'unique:tags,name,' . $id],
        ];
    }
}
