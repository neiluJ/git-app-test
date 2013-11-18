<?php
namespace TestGit\Form;

use Fwk\Form\Sanitizer;

/**
 * Trims the given value
 * 
 */
class SshKeySanitizer implements Sanitizer
{
    public function sanitize($value) {
        return trim($value);
    }
}