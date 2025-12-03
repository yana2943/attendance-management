<?php

namespace App\Http\Requests;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class CustomLoginRequest extends FortifyLoginRequest
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
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'is_admin_login' => ['nullable', 'boolean'],
        ];
    }

    public function isAdminLogin()
    {
        return $this->boolean('is_admin_login', false);
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }
}
