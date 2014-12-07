<?php
namespace TestGit\Model\User;

use Fwk\Security\User as UserInterface;
use Fwk\Security\User\PasswordAware;
use Fwk\Security\User\AclAware;
use Fwk\Db\Relations\Many2Many;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Tables;
use Fwk\Db\Relation;
use Fwk\Db\Relations\One2Many;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class User implements UserInterface, PasswordAware, 
    AclAware, ResourceInterface 
{
    const TYPE_USER = 'user';
    const TYPE_ORG  = 'organization';
    
    protected $id;
    protected $type;
    protected $username;
    protected $password;
    protected $http_password;
    protected $slug;
    protected $email;
    protected $date_registration;
    protected $date_activation;
    protected $hash;
    protected $active;
    protected $rolesRelation;
    protected $fullname;
    
    protected $notifications;
    
    protected $sshKeys;
    
    protected $accesses;

    protected $orgAccesses;
    protected $members;
    protected $repositories;

    public function __construct()
    {
        $this->rolesRelation = new Many2Many(
            'id', 
            'user_id', 
            Tables::ACL_ROLES, 
            Tables::USERS_ROLES, 
            'role', 
            'role',
            AclDao::ENTITY_ROLE
        );
        
        $this->rolesRelation->setFetchMode(Relation::FETCH_LAZY);
        
        $this->sshKeys = new One2Many('id', 'user_id', Tables::SSH_KEYS);
        $this->sshKeys->setFetchMode(Relation::FETCH_LAZY);
        
        $this->accesses = new One2Many('id', 'user_id', Tables::ACCESSES, 'TestGit\\Model\\Git\\Access');
        $this->accesses->setFetchMode(Relation::FETCH_LAZY);

        $this->orgAccesses = new One2Many('id', 'user_id', Tables::ORG_USERS, 'TestGit\\Model\\User\\OrgAccess');
        $this->orgAccesses->setFetchMode(Relation::FETCH_LAZY);

        $this->members = new One2Many('id', 'organization_id', Tables::ORG_USERS, 'TestGit\\Model\\User\\OrgAccess');
        $this->members->setFetchMode(Relation::FETCH_LAZY);
        $this->members->setReference('user_id');
        
        $this->repositories = new One2Many('id', 'owner_id', Tables::REPOSITORIES, GitDao::ENTITY_REPO);
        $this->repositories->setFetchMode(Relation::FETCH_LAZY);
    }
    
    public function getIdentifier()
    {
        return $this->id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getDate_registration()
    {
        return $this->date_registration;
    }

    public function setDate_registration($date_registration)
    {
        $this->date_registration = $date_registration;
    }

    public function getDate_activation()
    {
        return $this->date_activation;
    }

    public function setDate_activation($date_activation)
    {
        $this->date_activation = $date_activation;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getActive()
    {
        return $this->active;
    }
    
    public function isActive()
    {
        return (bool)$this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getRoleId()
    {
        return ($this->isUser() ? 'user' : 'organization') . ':'. $this->username;
    }
    
    public function getResourceId()
    {
        return ($this->isUser() ? 'user' : 'organization') .':'. $this->username;
    }

    public function getRoles()
    {
        $this->rolesRelation->fetch();
        return $this->rolesRelation->toArray();
    }
    
    /**
     *
     * @return Many2Many
     */
    public function getRolesRelation()
    {
        return $this->rolesRelation;
    }
    
    public function getFullname() {
        return $this->fullname;
    }

    public function setFullname($fullname) {
        $this->fullname = $fullname;
    }
    
    /**
     *
     * @return One2Many
     */
    public function getSshKeys() {
        return $this->sshKeys;
    }
    
    /**
     *
     * @return One2Many
     */
    public function getRepositories() {
        return $this->repositories;
    }

    public function getHttp_password()
    {
        return $this->http_password;
    }

    public function setHttp_password($http_password)
    {
        $this->http_password = $http_password;
    }
    
    public function getAccesses()
    {
        return $this->accesses;
    }
    
    public function isOrganization()
    {
        return $this->type === self::TYPE_ORG;
    }
    
    public function isUser()
    {
        return $this->type === self::TYPE_USER;
    }

    /**
     * @param \Fwk\Db\Relations\One2Many $orgAccesses
     */
    public function setOrgAccesses($orgAccesses)
    {
        $this->orgAccesses = $orgAccesses;
    }

    /**
     * @return \Fwk\Db\Relations\One2Many
     */
    public function getOrgAccesses()
    {
        return $this->orgAccesses;
    }

    /**
     * @param \Fwk\Db\Relations\One2Many $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

    /**
     * @return \Fwk\Db\Relations\One2Many
     */
    public function getMembers()
    {
        return $this->members;
    }

    public function displayName()
    {
        if (empty($this->fullname)) {
            return $this->username;
        }

        return $this->fullname;
    }

    public function isOrgMember(User $user)
    {
        if (!$this->isOrganization()) {
            return false;
        }

        $members = $this->members->fetch();
        foreach ($members as $member) {
            if ($member->getUser_id() == $user->getId()) {
                return true;
            }
        }

        return false;
    }
}