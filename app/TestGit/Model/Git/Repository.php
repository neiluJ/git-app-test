<?php
namespace TestGit\Model\Git;

use Fwk\Security\Acl\Manager;
use TestGit\Model\User\User;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Fwk\Db\Relations\One2One;
use TestGit\Model\Tables;
use TestGit\Model\User\UsersDao;
use Fwk\Db\Relation;
use Fwk\Db\Relations\One2Many;

class Repository implements ResourceInterface
{
    const TYPE_PRIVATE  = 'private';
    const TYPE_PUBLIC   = 'public';
    
    protected $id;
    protected $owner_id;
    protected $type;
    protected $public;
    protected $parent_id;
    protected $name;
    protected $fullname;
    protected $description;
    protected $website;
    protected $path;
    protected $default_branch;
    protected $created_at;
    protected $last_commit_date;
    protected $last_commit_hash;
    protected $last_commit_author;
    protected $last_commit_msg;
    protected $watchers = 0;
    protected $forks = 0;
    protected $languages;
    
    protected $owner;
    protected $parent;
    protected $accesses;
    protected $commits;
    protected $references;
    
    public function __construct()
    {
        $this->owner = new One2One(
            'owner_id', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
        
        $this->owner->setFetchMode(Relation::FETCH_EAGER);
        
        $this->parent = new One2One(
            'parent_id', 
            'id', 
            Tables::REPOSITORIES, 
            get_class($this)
        );
        
        $this->parent->setFetchMode(Relation::FETCH_LAZY);
        
        $this->accesses = new One2Many(
            'id', 
            'repository_id', 
            Tables::ACCESSES, 
            GitDao::ENTITY_ACCESS
        );
        
        $this->accesses->setFetchMode(Relation::FETCH_LAZY);
        
        $this->commits = new One2Many(
            'id',
            'repositoryId',
            Tables::COMMITS,
            GitDao::ENTITY_COMMIT
        );
        $this->commits->setFetchMode(Relation::FETCH_LAZY);
        
        
        $this->references = new One2Many(
            'id',
            'repositoryId',
            Tables::REFERENCES,
            GitDao::ENTITY_REFERENCE
        );
        $this->references->setFetchMode(Relation::FETCH_LAZY);
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getOwner_id() {
        return $this->owner_id;
    }

    public function setOwner_id($owner_id) {
        $this->owner_id = $owner_id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getPublic() {
        return $this->public;
    }

    public function setPublic($public) {
        $this->public = $public;
    }

    public function getParent_id() {
        return $this->parent_id;
    }

    public function setParent_id($parent_id) {
        $this->parent_id = $parent_id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getWebsite()
    {
        return $this->website;
    }
    
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getDefault_branch() {
        return $this->default_branch;
    }

    public function setDefault_branch($default_branch) {
        $this->default_branch = $default_branch;
    }

    public function getCreated_at() {
        return $this->created_at;
    }

    public function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    public function getLast_commit_date() {
        return $this->last_commit_date;
    }

    public function setLast_commit_date($last_commit_date) {
        $this->last_commit_date = $last_commit_date;
    }

    public function getLast_commit_hash() {
        return $this->last_commit_hash;
    }

    public function setLast_commit_hash($last_commit_hash) {
        $this->last_commit_hash = $last_commit_hash;
    }

    public function getLast_commit_author() {
        return $this->last_commit_author;
    }

    public function setLast_commit_author($last_commit_author) {
        $this->last_commit_author = $last_commit_author;
    }

    public function getLast_commit_msg() {
        return $this->last_commit_msg;
    }

    public function setLast_commit_msg($last_commit_msg) {
        $this->last_commit_msg = $last_commit_msg;
    }

    public function getWatchers() {
        return $this->watchers;
    }

    public function setWatchers($watchers) {
        $this->watchers = $watchers;
    }

    public function getForks()
    {
        return $this->forks;
    }

    public function setForks($forks)
    {
        $this->forks = $forks;
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    public function getResourceId()
    {
        return 'repo:'. $this->getFullname();
    }
    
    /**
     *
     * @return \TestGit\Model\User\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     *
     * @return One2One
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * 
     * @return One2Many
     */
    public function getAccesses()
    {
        return $this->accesses;
    }

        /**
     *
     * @return boolean
     */
    public function hasParent()
    {
        return !empty($this->parent_id);
    }
    
    public function isPrivate()
    {
        return !(bool)$this->public;
    }
    
    public function getFullname()
    {
        return $this->fullname;
    }
    
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }
    
    /**
     * 
     * @return One2Many
     */
    public function getCommits() {
        return $this->commits;
    }

    /**
     * 
     * @return One2Many
     */
    public function getReferences() {
        return $this->references;
    }

    public function loadAcls($user, Manager $acl) {
        if (!$acl->hasResource($this)) {
            $acl->addResource($this, 'repository');
        }

        if ($this->isPrivate()) {
            $acl->deny(null, $this);
        } else {
            $acl->allow(null, $this, 'read');
        }

        if (!$user instanceof User) {
            return;
        }

        foreach ($this->getAccesses() as $access) {
            if ($access->getUser_id() === $user->getId()) {
                if ($access->getReadAccess()) {
                    $acl->allow($user, $this, 'read');
                }
                if ($access->getWriteAccess()) {
                    $acl->allow($user, $this, 'write');
                }
                if ($access->getSpecialAccess()) {
                    $acl->allow($user, $this, 'special');
                }
                if ($access->getAdminAccess()) {
                    $acl->allow($user, $this, 'admin');
                }
            }
        }
        if ($user->getId() == $this->getOwner_id()) {
            $acl->allow($user, $this, 'owner');
        }
    }

    public function getGitName()
    {
        if (strpos($this->path, '.git', strlen($this->path)-4) !== false) {
            return substr($this->path, 0, strlen($this->path)-4);
        }

        return $this->path;
    }
}