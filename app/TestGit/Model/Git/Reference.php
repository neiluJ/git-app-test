<?php
namespace TestGit\Model\Git;

use Fwk\Db\Relations\One2One;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Tables;

class Reference
{
    const TYPE_BRANCH = 'branch';
    const TYPE_TAG  = 'tag';

    protected $id;
    protected $name;
    protected $fullname;
    protected $repositoryId;
    protected $pushId;
    protected $createdOn;
    protected $commitHash;
    protected $type;
    
    protected $repository;
    protected $commit;

    public function __construct()
    {
        $this->repository = new One2One(
            'id', 
            'repositoryId', 
            Tables::REPOSITORIES, 
            GitDao::ENTITY_REPO
        );
        
        $this->commit = new One2One(
            'commitHash', 
            'hash', 
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

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
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

    public function getCommitHash() {
        return $this->commitHash;
    }

    public function setCommitHash($commitHash)
    {
        $this->commitHash = $commitHash;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function getPushId() {
        return $this->pushId;
    }

    public function setPushId($pushId) {
        $this->pushId = $pushId;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function getCommit()
    {
        return $this->commit;
    }
    
    public function isBranch()
    {
        return $this->type === "branch";
    }
}