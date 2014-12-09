<?php
namespace TestGit\Model\User;

use TestGit\Model\Tables;
use Fwk\Db\Relation;
use Fwk\Db\Relations\One2One;

class OrgAccess
{
    protected $user_id;
    protected $organization_id;
    protected $added_by;

    protected $reposWriteAccess;
    protected $reposAdminAccess;
    protected $membersAdminAccess;
    protected $adminAccess;
    
    protected $user;
    protected $organization;
    
    public function __construct()
    {
        $this->user = new One2One(
            'user_id', 
            'id', 
            Tables::USERS, 
            UsersDao::ENTITY_USER
        );
        $this->organization = new One2One(
            'organization_id',
            'id',
            Tables::USERS,
            UsersDao::ENTITY_USER
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

    /**
     * @param mixed $added_by
     */
    public function setAdded_by($added_by)
    {
        $this->added_by = $added_by;
    }

    /**
     * @return mixed
     */
    public function getAdded_by()
    {
        return $this->added_by;
    }

    /**
     * @param mixed $membersAdminAccess
     */
    public function setMembersAdminAccess($membersAdminAccess)
    {
        $this->membersAdminAccess = $membersAdminAccess;
    }

    /**
     * @return mixed
     */
    public function getMembersAdminAccess()
    {
        return $this->membersAdminAccess;
    }

    /**
     * @param mixed $organization
     */
    public function setOrganization(One2One $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization_id
     */
    public function setOrganization_id($organization_id)
    {
        $this->organization_id = $organization_id;
    }

    /**
     * @return mixed
     */
    public function getOrganization_id()
    {
        return $this->organization_id;
    }

    /**
     * @param mixed $reposAdminAccess
     */
    public function setReposAdminAccess($reposAdminAccess)
    {
        $this->reposAdminAccess = $reposAdminAccess;
    }

    /**
     * @return mixed
     */
    public function getReposAdminAccess()
    {
        return $this->reposAdminAccess;
    }

    /**
     * @param mixed $reposWriteAccess
     */
    public function setReposWriteAccess($reposWriteAccess)
    {
        $this->reposWriteAccess = $reposWriteAccess;
    }

    /**
     * @return mixed
     */
    public function getReposWriteAccess()
    {
        return $this->reposWriteAccess;
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
}