<?php
namespace TestGit\Form;

use Fwk\Form\Filter;

/**
 */
class RepoNameFilter implements Filter
{
    const REPO_REGEX = '/^[^\-][a-z0-9\-\_]+$/';
    
    /**
     * 
     * @param string $value Repository name to validate
     * 
     * @return boolean
     */
    public function validate($value = null)
    {
        // vérification de la longueur
        if (strlen($value) < 2 || strlen($value) > 50) {
            return false;
        }
        
        return preg_match(self::REPO_REGEX, $value);
    }
}