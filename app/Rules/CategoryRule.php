<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CategoryRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return strtolower($value) == 'null' || strtolower($value) == '!null' || ctype_digit($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be `null`, `!null`, or an unsigned integer.';
    }
}
