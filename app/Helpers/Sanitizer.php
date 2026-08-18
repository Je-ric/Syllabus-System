<?php

namespace App\Helpers;

class Sanitizer
{
    /**
     * Sanitize user input for safe display in HTML contexts
     * Removes HTML tags, scripts, and special characters that could be used for XSS
     * 
     * @param string|null $input
     * @return string
     */
    public static function clean(?string $input): string
    {
        if ($input === null) {
            return '';
        }

        // Remove any remaining potentially dangerous patterns first
        $cleaned = preg_replace('/javascript:/i', '', $input);
        $cleaned = preg_replace('/on\w+\s*=/i', '', $cleaned);
        
        // Remove HTML tags and special characters
        $cleaned = strip_tags($cleaned);
        
        // Convert special characters to HTML entities
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return trim($cleaned);
    }

    /**
     * Sanitize input for use in JavaScript contexts
     * 
     * @param string|null $input
     * @return string
     */
    public static function cleanForJs(?string $input): string
    {
        if ($input === null) {
            return '';
        }

        // First escape quotes and backslashes for JavaScript
        $cleaned = str_replace('\\', '\\\\', $input);
        $cleaned = str_replace('"', '\\"', $cleaned);
        $cleaned = str_replace("'", "\\'", $cleaned);
        $cleaned = str_replace("\n", '\\n', $cleaned);
        $cleaned = str_replace("\r", '\\r', $cleaned);
        $cleaned = str_replace("\t", '\\t', $cleaned);
        
        // Then apply general cleaning
        $cleaned = self::clean($cleaned);
        
        return $cleaned;
    }

    /**
     * Validate that a name contains only letters and spaces
     * 
     * @param string $name
     * @return bool
     */
    public static function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[\p{L}\s]+$/u', $name) && strlen(trim($name)) > 0;
    }

    /**
     * Sanitize and validate a name field
     * 
     * @param string|null $name
     * @return string
     */
    public static function sanitizeName(?string $name): string
    {
        if ($name === null) {
            return '';
        }

        // First apply general cleaning to remove XSS attempts
        $cleaned = self::clean($name);
        
        // Then remove any numbers or special characters (keeping only letters and spaces)
        $cleaned = preg_replace('/[^\p{L}\s]/u', '', $cleaned);
        
        // Remove extra spaces
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        
        return trim($cleaned);
    }
}