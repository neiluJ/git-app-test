<?php
namespace TestGit\Form;

use Fwk\Form\Filter;

/**
 * Assure la validité d'un nom utilisateur.
 * Pour etre valide, il doit:
 * 
 * - Contenir entre 3 et 15 caractères alpha-numeriques et _-^[{]}\|`
 * - Ne commence pas par un chiffre ni par "-"
 * 
 * @todo Autoriser les users commencant par | ou \ (voir test)
 */
class UsernameFilter implements Filter
{
    const USER_REGEX = '#^([^0-9|\-][a-z0-9\-\_\^\`\[\]\{\}\|][^\s/])+#i'; // possibly buggy...
    
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
        if (strlen($value) < 3 || strlen($value) > 15) {
            return false;
        }
        
        // vérification du reste
        if (!preg_match(self::USER_REGEX, $value)) {
            return false;
        }
        
        // prevent usage of 'daemon' as its used for git-smart-http
        if ($value == 'daemon') {
            return false;
        }
        
        return true;
    }
}