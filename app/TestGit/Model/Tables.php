<?php
namespace TestGit\Model;

final class Tables
{
    const USERS         = 'users';
    const USERS_ROLES   = 'users_roles';
    const SSH_KEYS      = 'users_ssh_keys';
    
    const ACL_ROLES     = 'acls_roles';
    const ACL_RESOURCES = 'acls_resources';
    const ACL_PERMISSIONS   = 'acls_permissions';
    
    const REPOSITORIES  = 'repositories';
    
    private function __construct()
    {
    }
}