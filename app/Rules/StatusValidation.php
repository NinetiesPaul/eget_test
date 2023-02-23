<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Tasks;

class StatusValidation implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        return in_array(strtolower($value), Tasks::getAllValidStatus());
    }

    public function message()
    {
        return 'suplied status is invalid';
    }
}
