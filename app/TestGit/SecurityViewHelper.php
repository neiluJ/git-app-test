<?php
namespace TestGit;

use Fwk\Core\Components\ViewHelper\ViewHelper;
use Fwk\Core\Components\ViewHelper\AbstractViewHelper;
use Zend\Permissions\Acl\Role\GenericRole;

class SecurityViewHelper extends AbstractViewHelper implements ViewHelper
{
    protected $securityService;
    protected $guestRole;
    
    public function __construct($securityService, $guestRole = 'guest')
    {
        $this->securityService = $securityService;
        $this->guestRole = $guestRole;
    }
    
    public function execute(array $arguments)
    {
        $resource   = (isset($arguments[0]) ? $arguments[0] : null);
        $privilege  = (isset($arguments[1]) ? $arguments[1] : null);
        
        $service = $this->getViewHelperService()
                    ->getApplication()
                    ->getServices()
                    ->get($this->securityService);
        
        $acl = $service->getAclManager();
        $role = (
           $service->getAuthenticationManager()->hasIdentity() ?
           $service->getUser($this->getViewHelperService()->getContext()->getRequest()) :
           new GenericRole($this->guestRole)
        );
        
        if (!$acl->hasRole($role)) {
            $acl->addRole($role);
        }
        
        $element = (isset($arguments[0]) ? $arguments[0] : null);
        $form = (isset($arguments[1]) ? $arguments[1] : null);
        
        return $acl->isAllowed($role, $resource, $privilege);
    }
}