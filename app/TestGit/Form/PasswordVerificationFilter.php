<?php
namespace Forgery\Forms;

use Fwk\Form\Filter;
use Forgery\User\User;
use Forgery\User\UtilsFactory;

class PasswordVerificationFilter implements Filter
{
    protected $user;
    
    public function __construct(User $user) {
        $this->user = $user;
    }

    public function validate($value = null)
    {
        $generator      = UtilsFactory::newPasswordGenerator();
        $saltClosure    = UtilsFactory::newSaltClosure();
        $generator->setSalt($saltClosure($this->user));
        
        return $generator->create($value) === $this->user->getPassword();
    }
}