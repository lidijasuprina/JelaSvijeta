<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class WithRule implements Rule
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
        $allowedValues = ['category', 'tags', 'ingredients'];
        $values = explode(',', $value);
        
        foreach ($values as $value) {
            if (!in_array($value, $allowedValues)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be `category`, `tags`, `ingredients` or a combination of the 3 separated by `,`.';
    }
}
