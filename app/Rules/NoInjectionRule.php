<?php

namespace App\Rules;

use App\Helpers\SecurityValidator;
use Illuminate\Contracts\Validation\ValidationRule;

class NoInjectionRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        if (SecurityValidator::containsAnyInjection($value)) {
            $type = SecurityValidator::getInjectionType($value);
            $fail("The {$attribute} field contains {$type} injection and is not allowed.");
        }
    }
}


