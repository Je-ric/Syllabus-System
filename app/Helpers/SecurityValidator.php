<?php

namespace App\Helpers;

class SecurityValidator
{
    /**
     * Patterns that indicate script injection attempts
     */
    private const SCRIPT_PATTERNS = [
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
        '/<object\b[^>]*>(.*?)<\/object>/is',
        '/<embed\b[^>]*>/is',
        '/<applet\b[^>]*>(.*?)<\/applet>/is',
        '/<meta\b[^>]*>/is',
        '/<link\b[^>]*>/is',
        '/<style\b[^>]*>(.*?)<\/style>/is',
        '/on\w+\s*=/i', // onclick, onerror, onload, etc.
        '/javascript:/i',
        '/vbscript:/i',
        '/data:text\/html/i',
        '/data:javascript/i',
        '/\$\(|\`|\$\{\{/', // template literals and template strings
    ];

    /**
     * Patterns that indicate SQL injection attempts
     */
    private const SQL_PATTERNS = [
        '/\b(OR|AND)\s+\d+\s*=\s*\d+/i',
        '/\b(OR|AND)\s+["\']?\w+["\']?\s*=\s*["\']?\w+["\']?/i',
        '/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|TRUNCATE|EXEC|EXECUTE)\b/i',
        '/--\s*$/',
        '/\/\*/',
        '/\*\//',
        '/;\s*$/',
        '/\bxp_cmdshell\b/i',
        '/\bsp_executesql\b/i',
        '/\bwaitfor\s+delay\b/i',
        '/\bcast\s*\(/i',
        '/\bconvert\s*\(/i',
        '/0x[0-9a-f]+/i', // hex encoded strings
        '/char\s*\(/i',
        '/concat\s*\(/i',
    ];

    /**
     * Patterns that indicate code injection attempts
     */
    private const CODE_PATTERNS = [
        '/<\?php/i',
        '/<\?=/i',
        '/\?>/i',
        '/<%/i',
        '/%>/i',
        '/\$\{.*?\}/', // expression language
        '/#\{.*?\}/', // interpolation
        '/\{\{.*?\}\}/', // template literals
        '/@\w+\(/i', // C# like function calls
        '/@System\./i', // C# System calls
        '/\\\\u[0-9a-f]{4}/i', // unicode escape sequences (escaped backslash)
    ];

    /**
     * Check if input contains any script injection patterns
     */
    public static function containsScriptInjection(string $input): bool
    {
        foreach (self::SCRIPT_PATTERNS as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if input contains any SQL injection patterns
     */
    public static function containsSqlInjection(string $input): bool
    {
        foreach (self::SQL_PATTERNS as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if input contains any code injection patterns
     */
    public static function containsCodeInjection(string $input): bool
    {
        foreach (self::CODE_PATTERNS as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if input contains any injection attempts (script, SQL, or code)
     */
    public static function containsAnyInjection(string $input): bool
    {
        return self::containsScriptInjection($input)
            || self::containsSqlInjection($input)
            || self::containsCodeInjection($input);
    }

    /**
     * Validate that text is safe (no injections)
     * Returns true if safe, false if injection detected
     */
    public static function isSafeText(string $input): bool
    {
        return !self::containsAnyInjection($input);
    }

    /**
     * Validate that a name contains only letters and spaces (no numbers, no special chars)
     */
    public static function isValidName(string $input): bool
    {
        return (bool) preg_match('/^[\p{L}\s]+$/u', $input) && strlen(trim($input)) >= 2;
    }

    /**
     * Validate that text contains only safe characters for descriptions
     * Allows letters, numbers, spaces, and basic punctuation but blocks scripts/code
     */
    public static function isValidDescription(string $input): bool
    {
        // First check for injection attempts
        if (self::containsAnyInjection($input)) {
            return false;
        }

        // Then check for allowed characters (letters, numbers, spaces, basic punctuation, newlines, tabs)
        // Using character classes for whitespace instead of explicit \n\r\t
        return (bool) preg_match('/^[\p{L}\p{N}\s\-\.\,\:\;\(\)\[\]\{\}\"\'\!\?]+$/u', $input);
    }

    /**
     * Get the first detected injection type for error messages
     */
    public static function getInjectionType(string $input): ?string
    {
        if (self::containsScriptInjection($input)) {
            return 'script';
        }
        if (self::containsSqlInjection($input)) {
            return 'SQL';
        }
        if (self::containsCodeInjection($input)) {
            return 'code';
        }
        return null;
    }

    /**
     * Custom validation rule for Laravel
     * Usage: 'field' => ['required', new \App\Helpers\Rules\NoInjectionRule()]
     */
    public static function validateNoInjection($attribute, $value, $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        if (self::containsAnyInjection($value)) {
            $type = self::getInjectionType($value);
            $fail("The {$attribute} field contains {$type} injection and is not allowed.");
        }
    }
}
