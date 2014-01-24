<?php
namespace TestGit\Form;

use Fwk\Form\Filter;

/**
 * Checks if the input string is not a reserved word for as a repository name
 * 
 */
class ReservedRepoNamesFilter implements Filter
{
    protected $reservedNames = array(
        'settings',
        'activity',
        'log',
        'branch',
        'branches',
        'tag',
        'tags',
        'release',
        'releases',
        'contributions',
        'follow',
        'repositories',
        'repository',
        'commit',
        'commits',
        'daemon',
        'creator'       /* gitolite specials (wild repos) */
    );
    
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
            return true;
        }
        
        return !in_array(strtolower($value), $this->reservedNames);
    }
}