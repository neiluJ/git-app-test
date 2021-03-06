<?php
namespace TestGit\Form;

use Fwk\Form\Filter;
use TestGit\Model\User\UsersDao;
use TestGit\Model\User\User;

/**
 */
class EmailAlreadyExistsFilter implements Filter
{
    protected $usersDao;
    protected $except;
    
    public function __construct(UsersDao $usersDao, User $except = null)
    {
        $this->usersDao = $usersDao;
        $this->except = $except;
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
            $find = $this->usersDao->findOne($value, UsersDao::FIND_EMAIL, false);
            
            if (null !== $this->except && $find->getId() === $this->except->getId()) {
                return true;
            }
        } catch(\Exception $e) {
            return true;
        }
        
        return false;
    }
}