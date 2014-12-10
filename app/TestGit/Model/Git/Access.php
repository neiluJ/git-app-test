<?php
namespace TestGit\Model\Git;

use TestGit\Model\User\UsersDao;
use TestGit\Model\Tables;
use TestGit\Model\Git\GitDao;
use Fwk\Db\Relation;
use Fwk\Db\Relations\One2One;

class Access
{
    protected $user_id;
    protected $repository_id;
    protected $readAccess;
    protected $writeAccess;
    protected $specialAccess;
    protected $adminAccess;
    
    protected $user;
    protected $repository;
    
    public function __construct()
    {
        $this->user = new One2One(
            'user_id', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
        $this->repository = new One2One(
            'repository_id', 
            'id', 
            Tables::REPOSITORIES, 
            GitDao::ENTITY_REPO
        );
    }
    
    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getRepository_id()
    {
        return $this->repository_id;
    }

    public function setRepository_id($repository_id)
    {
        $this->repository_id = $repository_id;
    }

    public function getReadAccess()
    {
        return $this->readAccess;
    }

    public function setReadAccess($read)
    {
        $this->readAccess = $read;
    }

    public function getWriteAccess()
    {
        return $this->writeAccess;
    }

    public function setWriteAccess($write)
    {
        $this->writeAccess = $write;
    }

    public function getSpecialAccess()
    {
        return $this->specialAccess;
    }

    public function setSpecialAccess($special)
    {
        $this->specialAccess = $special;
    }

    public function getAdminAccess()
    {
        return $this->adminAccess;
    }

    public function setAdminAccess($admin)
    {
        $this->adminAccess = $admin;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRepository()
    {
        return $this->repository;
    }
    
    public function getGitoliteAccessString()
    {
        return sprintf(
            '%s%s%s', 
            ($this->readAccess == true ? 'R' : ''),
            ($this->writeAccess == true ? 'W' : ''),
            ($this->specialAccess == true ? '+' : '')    
        );
    }
}