<?php
namespace TestGit\Form;

use Fwk\Form\Filter;
use TestGit\Model\User\UsersDao;
use Fwk\Security\Service;
use TestGit\Model\User\UtilsFactory;

class AuthenticationFilter implements Filter
{
    protected $usersDao;
    protected $security;
    
    public function __construct(UsersDao $usersDao, Service $security) {
        $this->usersDao = $usersDao;
        $this->security = $security;
    }

    public function validate($form = null)
    {
        if (null === $form 
            || !$form->isSubmitted() 
            || $form->hasErrors()
        ) {
            return false;
        }
        
        $result     = $this->security->authenticate(
            UtilsFactory::newLoginFormAdapter(
                $form->username, 
                $form->password, 
                $this->usersDao
            )
        );
        
        return $result !== null;
    }
}