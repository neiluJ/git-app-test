<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use TestGit\Model\User\User;
use Fwk\Di\Container;

class UserChangePasswordEvent extends Event
{
    public function __construct(User $user, Container $services = null)
    {
        parent::__construct('userChangePassword', array(
            'user'      => $user,
            'services'  => $services
        ));
    }
    
    /**
     * 
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * 
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }
}