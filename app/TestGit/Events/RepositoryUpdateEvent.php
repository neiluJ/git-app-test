<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use Fwk\Di\Container;
use TestGit\Model\Git\Repository;

class RepositoryUpdateEvent extends Event
{
    public function __construct(Repository $repository, 
        Container $services = null, $username = null
    ) {
        parent::__construct('repositoryUpdate', array(
            'repository'  => $repository,
            'services'    => $services,
            'username'    => $username
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
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}