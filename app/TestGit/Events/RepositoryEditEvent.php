<?php
namespace TestGit\Events;

use Fwk\Events\Event;
use Fwk\Di\Container;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\User;

class RepositoryEditEvent extends Event
{
    public function __construct(Repository $repository, User $committer, $reason, Container $services = null)
    {
        parent::__construct('repositoryEdit', array(
            'repository'    => $repository,
            'reason'        => $reason,
            'committer'     => $committer,
            'services'      => $services
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
    public function getReason()
    {
        return $this->reason;
    }
    
    /**
     * 
     * @return User
     */
    public function getCommitter()
    {
        return $this->committer;
    }
}