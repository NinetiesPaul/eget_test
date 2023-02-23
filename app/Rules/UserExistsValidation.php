<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UserExistsValidation implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        return User::where('id', $value)->first();
    }

    public function message()
    {
        return 'supplied user_id is invalid';
    }
}
