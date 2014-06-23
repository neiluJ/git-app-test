<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use Fwk\Di\Container;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\User;

class RepositoryDeleteEvent extends Event
{
    public function __construct(Repository $repository, User $sender, Container $services = null)
    {
        parent::__construct('repositoryDelete', array(
            'repository'  => $repository,
            'services'    => $services,
            'sender'      => $sender
        ));
    }
    
    /**
     * 
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * 
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }
}