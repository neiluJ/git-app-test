<?php
namespace TestGit\Model\User;

use TestGit\Model\Dao;
use Fwk\Security\Acl\Provider;
use Zend\Permissions\Acl\Role\RoleInterface;
use Fwk\Db\Connection;
use Fwk\Db\Query;
use TestGit\Model\Tables;

class AclDao extends Dao implements Provider
{
    const ENTITY_ROLE       = 'TestGit\\Model\\User\\Role';
    const ENTITY_RESOURCE   = 'Forgery\\Model\\User\\Resource';
    
    /**
     * Constructeur 
     * 
     * @param Connection $connection Connexion à la base de donnée
     * @param array      $options    Options de configuration
     * 
     * @return void
     */
    public function __construct(Connection $connection = null, 
        $options = array())
    {
        $options = array_merge(array(
            'rolesTable'        => Tables::ACL_ROLES,
            'resourcesTable'    => Tables::ACL_RESOURCES,
            'permissionsTable'  => Tables::ACL_PERMISSIONS
        ), $options);
        
        parent::__construct($connection, $options);
    }
    
    public function getResourcesAll()
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('resourcesTable'))
                ->orderBy('sort', 'asc')
                ->entity(self::ENTITY_RESOURCE);
        
        $roles  = $this->getDb()->execute($query);
        $final  = array();
        
        foreach ($roles as $resource) {
            $final[] = array(
                'resource'  => $resource,
                'parents'   => $resource->getParent()
            );
        }
        
        return $final;
    }
    
    public function getPermissionsAll()
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('permissionsTable'));
        
        $perms  = $this->getDb()->execute($query);
        $final  = array();
        foreach ($perms as $permission) {
            $final[] = array(
                'role'      => $permission->role,
                'rule'      => ($permission->type == 'allow' ? 
                    'TYPE_ALLOW' : 
                    'TYPE_DENY'
                ),
                'resource'  => $permission->resource,
                'what'      => $permission->permission,
                'assert'    => null
            );
        }
        
        return $final;
    }
    
    public function getPermissions(RoleInterface $role)
    {
        return array();
    } 
    
    public function getResources(RoleInterface $role)
    {
        return array();
    }
    
    public function getRoles()
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('rolesTable'))
                ->orderBy('sort', 'asc')
                ->entity(self::ENTITY_ROLE);
        
        $roles  = $this->getDb()->execute($query);
        $final  = array();
        
        foreach ($roles as $role) {
            $final[] = array(
                'role'      => $role,
                'parents'   => $role->getParent()
            );
        }
        
        return $final;
    }
    
    public function getDefaultRoles()
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('rolesTable'))
                ->orderBy('sort', 'asc')
                ->entity(self::ENTITY_ROLE)
                ->where('`default` = ?');
        
        return $this->getDb()->execute($query, array(1));
    }
}