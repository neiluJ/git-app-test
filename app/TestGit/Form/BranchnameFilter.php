<?php
namespace TestGit\Form;

use Fwk\Form\Filter;

/**
 */
class BranchnameFilter implements Filter
{
    const BRANCH_REGEX = UsernameFilter::USER_REGEX;
    
    /**
     * Performe la validation
     * 
     * @param string $value Username to validate
     * 
     * @return boolean
     */
    public function validate($value = null)
    {
        // vérification de la longueur
        if (strlen($value) < 3 || strlen($value) > 150) {
            return false;
        }
        
        // vérification du reste
        if (!preg_match(self::BRANCH_REGEX, $value)) {
            return false;
        }
        
        return true;
    }
}