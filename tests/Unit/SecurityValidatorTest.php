<?php

namespace Tests\Unit;

use App\Helpers\SecurityValidator;
use PHPUnit\Framework\TestCase;

class SecurityValidatorTest extends TestCase
{
    public function test_detects_script_injection()
    {
        $scriptInputs = [
            '<script>alert("xss")</script>',
            '<script>document.location="http://evil.com"</script>',
            '<iframe src="http://evil.com"></iframe>',
            'javascript:alert("xss")',
            'onclick="alert("xss")"',
            'onerror="alert("xss")"',
            '<img src=x onerror="alert(1)">',
            'data:text/html,<script>alert(1)</script>',
        ];

        foreach ($scriptInputs as $input) {
            $this->assertTrue(
                SecurityValidator::containsScriptInjection($input),
                "Failed to detect script injection in: {$input}"
            );
        }
    }

    public function test_detects_sql_injection()
    {
        $sqlInputs = [
            "' OR '1'='1",
            "' OR 1=1--",
            "1' UNION SELECT * FROM users--",
            "'; DROP TABLE users; --",
            "admin'--",
            "' OR '1'='1' /*",
            "1; EXEC xp_cmdshell('dir')",
            "' OR 1=1#",
            "0x48656C6C6F", // hex encoded
        ];

        foreach ($sqlInputs as $input) {
            $this->assertTrue(
                SecurityValidator::containsSqlInjection($input),
                "Failed to detect SQL injection in: {$input}"
            );
        }
    }

    public function test_detects_code_injection()
    {
        $codeInputs = [
            '<?php system("ls"); ?>',
            '<?="hello"?>',
            '<%= "hello" %>',
            '${7*7}',
            '#{7*7}',
            '{{7*7}}',
            '@System.Console.Write("test")',
            '\\u003Cscript\\u003Ealert(1)\\u003C/script\\u003E',
        ];

        foreach ($codeInputs as $input) {
            $this->assertTrue(
                SecurityValidator::containsCodeInjection($input),
                "Failed to detect code injection in: {$input}"
            );
        }
    }

    public function test_allows_safe_text()
    {
        $safeInputs = [
            'This is a safe description.',
            'Course: Introduction to Computer Science',
            'Goal: Improve student outcomes',
            'Objective: Enhance learning experience',
            'College of Engineering',
            'Department of Computer Science',
            'Program: BS Computer Science',
            'Event: Final Examination Week',
            'Regular text with numbers: 123, 456',
            'Punctuation: hello, world. (test)',
        ];

        foreach ($safeInputs as $input) {
            $this->assertTrue(
                SecurityValidator::isSafeText($input),
                "Incorrectly blocked safe text: {$input}"
            );
        }
    }

    public function test_validates_names_correctly()
    {
        $validNames = [
            'John Doe',
            'María García',
            'José Rizal',
            'Juan Dela Cruz',
        ];

        foreach ($validNames as $name) {
            $this->assertTrue(
                SecurityValidator::isValidName($name),
                "Incorrectly rejected valid name: {$name}"
            );
        }

        $invalidNames = [
            'John123',
            'John@Doe',
            'John_Doe',
            'John-Doe',
            'John.Doe',
            'John1',
        ];

        foreach ($invalidNames as $name) {
            $this->assertFalse(
                SecurityValidator::isValidName($name),
                "Incorrectly accepted invalid name: {$name}"
            );
        }
    }

    public function test_validates_descriptions_correctly()
    {
        $validDescriptions = [
            'This is a valid description with punctuation.',
            'Course: Introduction to Programming (3 units)',
            'Goal: To improve student learning outcomes.',
            "Multi-line description with\nnewlines and\ttabs.",
        ];

        foreach ($validDescriptions as $desc) {
            $this->assertTrue(
                SecurityValidator::isValidDescription($desc),
                "Incorrectly rejected valid description: {$desc}"
            );
        }

        $invalidDescriptions = [
            '<script>alert("xss")</script>',
            'Description with <script> tags',
            'Description with javascript:alert(1)',
            'Description with <?php tags ?>',
        ];

        foreach ($invalidDescriptions as $desc) {
            $this->assertFalse(
                SecurityValidator::isValidDescription($desc),
                "Incorrectly accepted invalid description: {$desc}"
            );
        }
    }

    public function test_get_injection_type()
    {
        $this->assertEquals('script', SecurityValidator::getInjectionType('<script>alert(1)</script>'));
        $this->assertEquals('SQL', SecurityValidator::getInjectionType("' OR '1'='1"));
        $this->assertEquals('code', SecurityValidator::getInjectionType('<?php echo "test"; ?>'));
        $this->assertNull(SecurityValidator::getInjectionType('Safe text'));
    }
}
