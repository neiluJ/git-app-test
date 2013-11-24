<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use Fwk\Di\Container;
use TestGit\Model\Git\Repository;

class RepositoryDeleteEvent extends Event
{
    public function __construct(Repository $repository, Container $services = null)
    {
        parent::__construct('repositoryDelete', array(
            'repository'  => $repository,
            'services'    => $services
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
}