<?php

namespace App\Http\Requests\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class SaveRequest extends FormRequest
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
        $user = request()->user;
        $id = $user ? $user->id : null;
        $createRules = [];
        $updateRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
        ];

        if (!$user) {//if user exist, it means we update user and don't need to require password
            $createRules = [
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ];
        }

        return array_merge($updateRules, $createRules);
    }
}
