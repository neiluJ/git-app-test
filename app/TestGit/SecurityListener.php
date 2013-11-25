<?php
namespace TestGit;

use Fwk\Core\Events\BeforeActionEvent;
use Fwk\Security\Exceptions\AuthenticationRequired;
use Fwk\Xml\Map, Fwk\Xml\Path;
use Fwk\Core\Components\Descriptor\DescriptorLoadedEvent;
use Fwk\Core\Events\ErrorEvent;
use Fwk\Core\Components\Descriptor\Descriptor;
use Fwk\Security\Service as SecurityService;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use \RuntimeException as Exception;
use Fwk\Core\Components\UrlRewriter\UrlRewriterService;
use Fwk\Core\Components\RequestMatcher\RequestMatcher;
use Fwk\Di\Container;
class SecurityListener
{
    protected $serviceName;
    protected $descriptor;
    protected $guestRole;
    protected $urlRewriter;
    protected $requestMatcher;
    protected $loginAction;
    protected $redirectParam;
    
    public function __construct($serviceName, $requestMatcher, 
        $urlRewriter = null, $guestRole = 'guest', $loginAction = 'Login', 
        $redirectParam = 'back'
    ) {
        $this->serviceName      = $serviceName;
        $this->guestRole        = $guestRole;
        $this->requestMatcher   = $requestMatcher;
        $this->urlRewriter      = $urlRewriter;
        $this->loginAction      = $loginAction;
        $this->redirectParam    = $redirectParam;
    }
    
    public function onDescriptorLoaded(DescriptorLoadedEvent $event)
    {
        $this->descriptor = $event->getDescriptor();
    }
    
    public function onBeforeAction(BeforeActionEvent $event)
    {
        $actionName = $event->getContext()->getActionName();
        $rules      = $this->calculateAclsForAction($actionName);
        
        if (!count($rules)) {
            return;
        }
        
        $service = $event->getApplication()->getServices()->get($this->serviceName);
        if (!$service instanceof SecurityService) {
            return;
        }
        
        $resource = 'action:'. $actionName;
        $acl = $service->getAclManager();
        if (!$acl->hasResource($resource)) {
            $acl->addResource(new GenericResource($resource));
        }
        
        $role = (
           $service->getAuthenticationManager()->hasIdentity() ?
           $service->getUser($event->getContext()->getRequest()) :
           new GenericRole($this->guestRole)
        );
        
        if (!empty($role) && !$acl->hasRole($role)) {
            $acl->addRole($role);
        }
        
        foreach ($rules as $data) {
            $type = strtolower($data['type']);
            if ($type === "allow") {
                $acl->allow((empty($data['role']) ? null : $data['role']), $resource, 'view');
            } elseif ($type === "deny") {
                $acl->deny((empty($data['role']) ? null : $data['role']), $resource, 'view');
            }
        }
        
        $allowed = $acl->isAllowed($role, $resource, 'view');
        if ($allowed) {
            return;
        }
        
        if (!$service->getAuthenticationManager()->hasIdentity()) {
            throw new AuthenticationRequired();
        } else {
            if (!$event->getContext()->hasParent()) {
                $response = new Response("<html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>You are not allowed to see this page</p></body></html>", "403");
            } else {
                $response = new Response();
            }
            
            $event->getContext()->setResponse($response);
        } 
    }
    
    protected function calculateAclsForAction($actionName)
    {
        if (!$this->descriptor instanceof Descriptor) {
            return array();
        }
        
        $final = array();
        foreach ($this->descriptor->getSourcesXml() as $xml) {
            $acls = $this->getActionAclsMap($actionName)->execute($xml);
            if (!isset($acls['acls'])) {
                continue;
            }
            
            $final += $acls['acls'];
        }
        
        return $final;
    }
    
    protected function calculateRedirectUri($actionName, Container $services,
        array $params = array()
    ) {
        $uri = false;
        if (null !== $this->urlRewriter) {
            $service = $services->get($this->urlRewriter);
            if (!$service instanceof UrlRewriterService) {
                throw new Exception(
                    sprintf(
                        '"%s" is not an UrlRewriterService instance', 
                        $this->urlRewriter
                    )
                );
            }
            
            $uri = $service->reverse($actionName, $params, false);
        } 
        
        if ($uri === false && null !== $this->requestMatcher) {
            $service = $services->get($this->requestMatcher);
            if (!$service instanceof RequestMatcher) {
                throw new Exception(
                    sprintf(
                        '"%s" is not an RequestMatcher instance', 
                        $this->requestMatcher
                    )
                );
            }
            
            $uri = $service->reverse($actionName, $params, false);
        } else {
            throw new Exception('You must specify at least a RequestMatcher Service');
        }
        
        return $uri;
    }
    
    /**
    *
    * @return Map
    */
    private function getActionAclsMap($actionName)
    {
        $map = new Map();
        $map->add(
            Path::factory(
                sprintf("/fwk/actions/action[@name='%s']/acl", $actionName),
                'acls'
            )
            ->loop(true)
            ->attribute('type') // allow/deny
            ->attribute('role') // roleName
        );

        return $map;
    }
    
    public function onError(ErrorEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof AuthenticationRequired) {
            return;
        }
        
        $event->stop();
        
        $request = $event->getContext()->getRequest();
        $response = new RedirectResponse($event->getContext()->getRequest()->getBaseUrl() . $this->calculateRedirectUri($this->loginAction, $event->getApplication()->getServices(), array(
            $this->redirectParam => $request->getRequestUri()
        )));
        
        $event->getContext()->setResponse($response);
    }
}