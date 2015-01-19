<?php
namespace TestGit\Model\User;

use Fwk\Db\Relations\One2One;
use TestGit\Model\Tables;
use TestGit\Model\User\UsersDao;

class Activity
{
    const DYN_REF_CREATE    = 'new-ref';
    const DYN_PUSH          = 'push';
    
    const REPO_CREATE       = 'create';
    const REPO_FORK         = 'fork';
    const REPO_DELETE       = 'delete';

    const REPO_COMMENT_COMMIT   = 'cmt-commit';
    const REPO_COMMENT_PR       = 'cmt-compare';

    const REPO_BRANCH_DELETE    = 'rm-branch';
    const REPO_TAG_DELETE       = 'rm-tag';

    protected $id;
    protected $userId;
    protected $repositoryId;
    protected $repositoryName;
    protected $targetId;
    protected $targetName;
    protected $targetUrl;
    protected $type;
    protected $message;
    protected $createdOn;
    
    protected $user;
    
    function __construct()
    {
        $this->user = new One2One(
            'userId', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getRepositoryId()
    {
        return $this->repositoryId;
    }

    public function setRepositoryId($repositoryId)
    {
        $this->repositoryId = $repositoryId;
    }

    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    public function setRepositoryName($repositoryName)
    {
        $this->repositoryName = $repositoryName;
    }

    public function getTargetId()
    {
        return $this->targetId;
    }

    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }

    public function getTargetName()
    {
        return $this->targetName;
    }

    public function setTargetName($targetName)
    {
        $this->targetName = $targetName;
    }

    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function setCreatedOn($createdOn) 
    {
        $this->createdOn = $createdOn;
    }
    
    /**
     * 
     * @return One2One
     */
    public function getUser()
    {
        return $this->user;
    }
}