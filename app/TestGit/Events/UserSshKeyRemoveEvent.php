<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use TestGit\Model\User\User;
use Fwk\Di\Container;

class UserSshKeyRemoveEvent extends Event
{
    public function __construct(User $user, \stdClass $sshKey, 
        Container $services = null
    ) {
        parent::__construct('userSshKeyRemove', array(
            'user'      => $user,
            'sshKey'    => $sshKey,
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
     * @return \stdClass
     */
    public function getSshKey()
    {
        return $this->sshKey;
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