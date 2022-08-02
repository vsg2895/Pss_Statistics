<?php

namespace App\Http\Requests\ServiceProvider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class SaveUser extends FormRequest
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
        //todo:fix email deleted at duplicate issue, try to add new user with the email from deleted_at user
        $user = $this->route('service_provider_user');
        $id = $user ? $user->id : null;
        $createRules = [];
        $updateRules = [
            'name' => ['required', 'sometimes', 'string', 'max:255'],
            'email' => ['required', 'sometimes', 'string', 'email', 'max:255', 'unique:service_provider_users,email,'.$id.',id,deleted_at,NULL'],
            'service_provider_id' => ['required', 'integer', 'exists:service_providers,id']
        ];

        if (!$user) {//if $provider exist, it means we update user and don't need to require password
            $createRules = [
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ];
        }

        return array_merge($updateRules, $createRules);
    }
}
