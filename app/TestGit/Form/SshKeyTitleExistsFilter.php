<?php
namespace TestGit\Form;

use Fwk\Form\Filter;
use TestGit\Model\User\UsersDao;
use TestGit\Model\User\User;

/**
 */
class SshKeyTitleExistsFilter implements Filter
{
    protected $usersDao;
    protected $user;
    
    public function __construct(UsersDao $usersDao, User $user)
    {
        $this->usersDao = $usersDao;
        $this->user = $user;
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
            $find = $this->usersDao->findSshKeyByTitleUser($value, $this->user);
        } catch(\Exception $e) {
            return false;
        }
        
        return count($find) === 0;
    }
}