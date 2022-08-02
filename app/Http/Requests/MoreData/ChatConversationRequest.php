<?php

namespace App\Http\Requests\MoreData;

use Illuminate\Foundation\Http\FormRequest;

class ChatConversationRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'chat_id' => ['required', 'exists:daily_chats,chat_id', 'sometimes'],
        ];
    }
}
