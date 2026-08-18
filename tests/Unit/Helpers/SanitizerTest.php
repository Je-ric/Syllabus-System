<?php

namespace Tests\Unit\Helpers;

use App\Helpers\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    public function test_clean_removes_xss_attempts()
    {
        $malicious = '<script>alert(1)</script>';
        $cleaned = Sanitizer::clean($malicious);
        
        $this->assertStringNotContainsString('<script>', $cleaned);
        $this->assertStringNotContainsString('</script>', $cleaned);
        // The text content "alert(1)" is safe as plain text
        $this->assertStringContainsString('alert(1)', $cleaned);
    }

    public function test_clean_handles_img_onerror()
    {
        $malicious = '<img src=x onerror=alert(1)>';
        $cleaned = Sanitizer::clean($malicious);
        
        $this->assertStringNotContainsString('<img', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
    }

    public function test_clean_handles_javascript_protocol()
    {
        $malicious = 'javascript:alert(1)';
        $cleaned = Sanitizer::clean($malicious);
        
        $this->assertStringNotContainsString('javascript:', $cleaned);
    }

    public function test_clean_handles_on_events()
    {
        $malicious = 'onclick="alert(1)"';
        $cleaned = Sanitizer::clean($malicious);
        
        $this->assertStringNotContainsString('onclick', $cleaned);
    }

    public function test_sanitize_name_allows_letters_and_spaces()
    {
        $validName = 'John Doe';
        $cleaned = Sanitizer::sanitizeName($validName);
        
        $this->assertEquals('John Doe', $cleaned);
    }

    public function test_sanitize_name_removes_numbers()
    {
        $nameWithNumbers = 'John123 Doe456';
        $cleaned = Sanitizer::sanitizeName($nameWithNumbers);
        
        $this->assertEquals('John Doe', $cleaned);
    }

    public function test_sanitize_name_removes_special_characters()
    {
        $nameWithSpecialChars = 'John!@# Doe$%^';
        $cleaned = Sanitizer::sanitizeName($nameWithSpecialChars);
        
        $this->assertEquals('John Doe', $cleaned);
    }

    public function test_sanitize_name_handles_xss_attempts()
    {
        $maliciousName = 'John<script>alert(1)</script> Doe';
        $cleaned = Sanitizer::sanitizeName($maliciousName);
        
        $this->assertStringNotContainsString('<script>', $cleaned);
        $this->assertStringNotContainsString('</script>', $cleaned);
        // The text content should be sanitized - script tags removed, letters only remain
        $this->assertMatchesRegularExpression('/^John[a-z\s]+Doe$/', $cleaned);
    }

    public function test_is_valid_name_returns_true_for_valid_names()
    {
        $this->assertTrue(Sanitizer::isValidName('John Doe'));
        $this->assertTrue(Sanitizer::isValidName('María García'));
        $this->assertTrue(Sanitizer::isValidName('José'));
    }

    public function test_is_valid_name_returns_false_for_invalid_names()
    {
        $this->assertFalse(Sanitizer::isValidName('John123'));
        $this->assertFalse(Sanitizer::isValidName('John!@#'));
        $this->assertFalse(Sanitizer::isValidName(''));
        $this->assertFalse(Sanitizer::isValidName(' '));
    }

    public function test_clean_returns_empty_string_for_null()
    {
        $this->assertEquals('', Sanitizer::clean(null));
    }

    public function test_cleanForJs_escapes_quotes()
    {
        $input = 'John "The Rock" Doe';
        $cleaned = Sanitizer::cleanForJs($input);
        
        // The function should escape quotes and make the string safe for JS
        $this->assertStringNotContainsString('"', $cleaned);
        // Since clean() is called after escaping, the backslash gets escaped as HTML entity
        $this->assertStringContainsString('\\&quot;', $cleaned);
    }
}