<?php
namespace TestGit\Model\User;

use Fwk\Security\User as UserInterface;
use Fwk\Security\User\PasswordAware;
use Fwk\Security\User\AclAware;
use Fwk\Db\Relations\Many2Many;
use TestGit\Model\Tables;
use Fwk\Db\Relation;
use Fwk\Db\Relations\One2Many;

class User implements UserInterface, PasswordAware, 
    AclAware
{
    protected $id;
    protected $username;
    protected $password;
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
        
        $this->accesses = new One2Many('id', 'user_id', Tables::ACCESSES);
        $this->accesses->setFetchMode(Relation::FETCH_LAZY);
        
        /*
        $this->repositories = new One2Many('id', 'owner_id', Tables::REPOSITORIES);
        $this->repositories->setFetchMode(Relation::FETCH_LAZY);
         */
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

    public function getRoleId()
    {
        return 'user:'. $this->username;
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


}