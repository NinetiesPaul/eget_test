<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OwnerValidation implements Rule
{
    public const OWNER_ME = "me";

    public const OWNER_OTHERS = "others";

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, [ self::OWNER_ME, self::OWNER_OTHERS]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'suplied owner is invalid';
    }
}
