<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:service_provider_users,email'],//todo check also deleted_at column
            'password' => ['required']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new Response(['message' => 'Invalid login details'], 422);
        throw new ValidationException($validator, $response);
    }
}
