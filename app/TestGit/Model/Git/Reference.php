<?php
namespace TestGit\Model\Git;

class Reference
{
    protected $id;
    protected $name;
    protected $repositoryId;
    protected $createdOn;
    protected $commitHash;
    
    protected $repository;
    protected $commit;
    
    public function __construct()
    {
        ;
    }
}