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
            $this->usersDao->findOne($value, UsersDao::FIND_EMAIL, false);
        } catch(\Exception $e) {
            return true;
        }
        
        return false;
    }
}