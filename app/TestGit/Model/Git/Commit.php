<?php
namespace TestGit\Model\Git;

use Fwk\Db\Relations\One2One;
use Fwk\Db\Relations\One2Many;
use TestGit\Model\User\UsersDao;
use TestGit\Model\Tables;
use Fwk\Db\Relations\Many2Many;

class Commit
{
    protected $id;
    protected $hash;
    protected $repositoryId;
    protected $pushId;
    
    protected $authorName;
    protected $authorDate;
    protected $authorEmail;
    
    protected $authorId;
    
    protected $committerName;
    protected $committerDate;
    protected $committerEmail;
    
    protected $committerId;
    
    protected $message;
    
    protected $indexDate;
    
    protected $repository;
    protected $author;
    protected $committer;
    protected $references;
    protected $push;
    
    public function __construct()
    {
        $this->repository = new One2One(
            'repositoryId', 
            'id', 
            Tables::REPOSITORIES, 
            GitDao::ENTITY_REPO
        );
        $this->repository->setFetchMode(One2One::FETCH_EAGER);
        
        $this->author = new One2One(
            'authorId', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
        
        $this->committer = new One2One(
            'committerId', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
        
        $this->push = new One2One(
            'pushId', 
            'id', 
            Tables::PUSHES, 
            GitDao::ENTITY_REPO
        );
        
        $this->references = new Many2Many(
            'id', 
            'commitId', 
            Tables::REFERENCES, 
            Tables::COMMITS_REFS, 
            'id', 
            'refId', 
            GitDao::ENTITY_REFERENCE
        );
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getHash() {
        return $this->hash;
    }

    public function setHash($hash) {
        $this->hash = $hash;
    }

    public function getRepositoryId() {
        return $this->repositoryId;
    }

    public function setRepositoryId($repositoryId) {
        $this->repositoryId = $repositoryId;
    }

    public function getPushId() {
        return $this->pushId;
    }

    public function setPushId($pushId) {
        $this->pushId = $pushId;
    }

    public function getAuthorName() {
        return $this->authorName;
    }

    public function setAuthorName($authorName) {
        $this->authorName = $authorName;
    }

    public function getAuthorDate() {
        return $this->authorDate;
    }

    public function setAuthorDate($authorDate) {
        $this->authorDate = $authorDate;
    }

    public function getAuthorEmail() {
        return $this->authorEmail;
    }

    public function setAuthorEmail($authorEmail) {
        $this->authorEmail = $authorEmail;
    }

    public function getAuthorId() {
        return $this->authorId;
    }

    public function setAuthorId($authorId) {
        $this->authorId = $authorId;
    }

    public function getCommitterName() {
        return $this->committerName;
    }

    public function setCommitterName($committerName) {
        $this->committerName = $committerName;
    }

    public function getCommitterDate() {
        return $this->committerDate;
    }

    public function setCommitterDate($committerDate) {
        $this->committerDate = $committerDate;
    }

    public function getCommitterEmail() {
        return $this->committerEmail;
    }

    public function setCommitterEmail($committerEmail) {
        $this->committerEmail = $committerEmail;
    }

    public function getCommitterId() {
        return $this->committerId;
    }

    public function setCommitterId($committerId) {
        $this->committerId = $committerId;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getIndexDate() {
        return $this->indexDate;
    }

    public function setIndexDate($indexDate) {
        $this->indexDate = $indexDate;
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
     * @return One2One
     */
    public function getCommitter()
    {
        return $this->committer;
    }

    /**
     *
     * @return One2One
     */
    public function getPush()
    {
        return $this->push;
    }
    
    /**
     *
     * @return Many2Many
     */
    public function getReferences()
    {
        return $this->references;
    }
    
    public function getCommitterDateObj()
    {
        return new \DateTime($this->committerDate);
    }
    
    public function getAuthorDateObj()
    {
        return new \DateTime($this->authorDate);
    }
    
    public function getComputedCommitterName()
    {
        if (empty($this->committerId)) {
            return (empty($this->committerName) ? $this->committerEmail : $this->committerName);
        }
        
        return $this->getCommitter()->get()->getFullname();
    }
}