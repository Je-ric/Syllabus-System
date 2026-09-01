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
     * Patterns that indicate SQL injection attempts.
     * These are scoped to actual SQL syntax, not standalone English words.
     */
    private const SQL_PATTERNS = [
        // Tautology patterns: OR 1=1, AND 1=1, OR 'a'='a'
        '/\b(OR|AND)\s+\d+\s*=\s*\d+/i',
        '/\b(OR|AND)\s+["\']\w+["\']\s*=\s*["\']\w+["\']\s*--/i',
        // UNION SELECT is the real attack vector, not UNION alone
        '/\bUNION\s+(ALL\s+)?SELECT\b/i',
        // DML/DDL only when followed by SQL-specific syntax
        '/\bSELECT\s+.+\s+FROM\b/i',
        '/\bINSERT\s+INTO\b/i',
        '/\bUPDATE\s+\w+\s+SET\b/i',
        '/\bDELETE\s+FROM\b/i',
        '/\bDROP\s+(TABLE|DATABASE|INDEX|VIEW|PROCEDURE)\b/i',
        '/\bALTER\s+(TABLE|DATABASE)\b/i',
        '/\bTRUNCATE\s+TABLE\b/i',
        '/\bEXEC(UTE)?\s*\(/i',
        // SQL comment sequences used to terminate queries
        '/--[\s\S]/',
        '/\/\*[\s\S]*?\*\//',
        // Dangerous stored procedures
        '/\bxp_cmdshell\b/i',
        '/\bsp_executesql\b/i',
        '/\bwaitfor\s+delay\b/i',
        // Hex-encoded payloads
        '/0x[0-9a-f]{4,}/i',
    ];

    /**
     * Patterns that indicate code injection attempts
     */
    private const CODE_PATTERNS = [
        '/<\?php/i',
        '/<\?=/i',
        '/<%[^=]/', // ASP-style tags, but not <%=
        '/%>/',
        '/\$\{.*?\}/', // expression language injection
        '/#\{.*?\}/', // EL interpolation
        '/@System\./i', // C# System calls
        '/\\u[0-9a-f]{4}/i', // unicode escape sequences
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

        // Then check for allowed characters (letters, numbers, spaces, and common punctuation)
        return (bool) preg_match('/^[\p{L}\p{N}\s\-\.\,\:\;\(\)\[\]\{\}\"\'\!\?\&\%\+\/\#\@\*\=]+$/u', $input);
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
