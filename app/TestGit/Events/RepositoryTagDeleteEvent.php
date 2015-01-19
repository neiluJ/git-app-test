<?php
namespace TestGit\Events;

use Fwk\Di\Container;
use Fwk\Events\Event;
use TestGit\Model\Git\Reference;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\User;

class RepositoryTagDeleteEvent extends Event
{
    public function __construct(Repository $repository, User $sender, Reference $ref, Container $services = null)
    {
        parent::__construct('repositoryTagDelete', array(
            'repository'  => $repository,
            'reference'   => $ref,
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

    /**
     *
     * @return Reference
     */
    public function getReference()
    {
        return $this->reference;
    }
}