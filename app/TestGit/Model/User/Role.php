<?php
namespace TestGit\Model\User;

use Zend\Permissions\Acl\Role\RoleInterface;

class Role implements RoleInterface
{
    /**
     * Unique id of Role
     *
     * @var string
     */
    protected $role;
    protected $description;
    protected $parent;
    protected $sort;

    /**
     * Defined by RoleInterface; returns the Role identifier
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->role;
    }

    /**
     * Defined by RoleInterface; returns the Role identifier
     * Proxies to getRoleId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->role;
    }
    
    public function getRole() 
    {
        return $this->role;
    }

    public function setRole($role) 
    {
        $this->role = $role;
    }

    public function getDescription() 
    {
        return $this->description;
    }

    public function setDescription($description) 
    {
        $this->description = $description;
    }

    public function getParent() 
    {
        return $this->parent;
    }

    public function setParent($parent) 
    {
        $this->parent = $parent;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }
}