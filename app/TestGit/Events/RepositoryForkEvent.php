<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use Fwk\Di\Container;
use TestGit\Model\Git\Repository;

class RepositoryForkEvent extends Event
{
    public function __construct(Repository $repository, Repository $fork, Container $services = null)
    {
        parent::__construct('repositoryFork', array(
            'repository'  => $repository,
            'fork'        => $fork,
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
    
    /**
     * 
     * @return Repository
     */
    public function getFork()
    {
        return $this->fork;
    }
}