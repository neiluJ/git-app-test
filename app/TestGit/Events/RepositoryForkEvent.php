<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use Fwk\Di\Container;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\User;

class RepositoryForkEvent extends Event
{
    public function __construct(Repository $repository, Repository $fork, User $sender, Container $services = null)
    {
        parent::__construct('repositoryFork', array(
            'repository'  => $repository,
            'fork'        => $fork,
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
     * 
     * @return Repository
     */
    public function getFork()
    {
        return $this->fork;
    }

    /**
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }
}