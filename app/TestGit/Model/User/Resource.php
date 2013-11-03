<?php
namespace TestGit\Model\User;

use Zend\Permissions\Acl\Resource\ResourceInterface;

class Resource implements ResourceInterface
{
    /**
     * Unique id of Resource
     *
     * @var string
     */
    protected $resource;
    protected $description;
    protected $parent;
    protected $sort;

    /**
     * Defined by RoleInterface; returns the Role identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->resource;
    }
    
    public function getResource() 
    {
        return $this->resource;
    }

    public function setResource($resource) 
    {
        $this->resource = $resource;
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