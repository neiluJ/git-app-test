<?php
namespace TestGit\Model\Git;

use Fwk\Db\Relations\One2One;
use Fwk\Db\Relations\One2Many;
use TestGit\Model\User\UsersDao;
use TestGit\Model\Tables;

class Push
{
    protected $id;
    protected $userId;
    protected $username;
    protected $repositoryId;
    protected $createdOn;
    
    protected $repository;
    protected $author;
    protected $commits;
    
    public function __construct()
    {
        $this->repository = new One2One(
            'repositoryId', 
            'id', 
            Tables::REPOSITORIES, 
            GitDao::ENTITY_REPO
        );
        
        $this->author = new One2One(
            'userId', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
        
        $this->commits = new One2Many(
            'id', 
            'pushId', 
            Tables::COMMITS, 
            GitDao::ENTITY_COMMIT
        );
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getRepositoryId() {
        return $this->repositoryId;
    }

    public function setRepositoryId($repositoryId) {
        $this->repositoryId = $repositoryId;
    }

    public function getCreatedOn() {
        return $this->createdOn;
    }

    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @return One2One
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     *
     * @return One2One
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     *
     * @return One2Many
     */
    public function getCommits() 
    {
        return $this->commits;
    }
}