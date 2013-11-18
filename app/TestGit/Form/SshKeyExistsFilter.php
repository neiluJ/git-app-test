<?php
namespace TestGit\Form;

use Fwk\Form\Filter;
use TestGit\Model\User\UsersDao;

/**
 */
class SshKeyExistsFilter implements Filter
{
    protected $usersDao;
    
    public function __construct(UsersDao $usersDao)
    {
        $this->usersDao = $usersDao;
    }
    
    /**
     * Performe la validation
     * 
     * @param string $value Username to validate
     * 
     * @return boolean
     */
    public function validate($value = null)
    {
        try {
            $find = $this->usersDao->findSshKeyByHash(md5($value));
        } catch(\Exception $e) {
            return false;
        }
        
        return count($find) === 0;
    }
}