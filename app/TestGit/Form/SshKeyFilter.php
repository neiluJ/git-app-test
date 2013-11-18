<?php
namespace TestGit\Form;

use Fwk\Form\Filter;

/**
 * Checks if the input string is a valid (Open)SSH-Key
 * 
 */
class SshKeyFilter implements Filter
{
    /**
     * Performs la validation
     * 
     * @param string $value Public Key String
     * 
     * @return boolean
     */
    public function validate($value = null)
    {
        if (empty($value)) {
            return false;
        }
        
        if (strpos($value, 'ssh-rsa ') !== 0) {
            return false;
        }
        
        list(,$base64,) = explode(' ', $value);
        if (!base64_decode($base64, true)) {
            return false;
        }
        
        return true;
    }
}