<?php
namespace TestGit\Controllers;

use Fwk\Core\Context;
use Fwk\Core\ContextAware;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;

class Home implements ServicesAware, ContextAware
{
    public $active;
    public $errorMsg;
    
    protected $services;
    protected $context;

    protected $user;
    protected $inChat = false;

    public $entity;
    public $emptyRepo = false;

    protected $debug = false;

    public function show()
    {
        return Result::SUCCESS;
    }
    
    public function menu()
    {
        return Result::SUCCESS;
    }
    
    public function userMenu()
    {
        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();
        } catch(\Exception $e) {
        }
        
        return Result::SUCCESS;
    }

    public function chatMenu()
    {
        $this->userMenu();
        $this->inChat = ($this->getContext()->hasParent() && $this->context->getParent()->getActionName() == "Chat");

        return Result::SUCCESS;
    }
    
    public function error()
    {
        $this->debug = $this->getServices()->getProperty('app.debug', false);

        if ($this->errorMsg instanceof \Exception && !$this->debug) {
            $this->errorMsg = $this->errorMsg->getMessage();
        }

        return Result::SUCCESS;
    }

    /**
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Container $services)
    {
        $this->services = $services;
    }
    
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return boolean
     */
    public function getInChat()
    {
        return $this->inChat;
    }

    /**
     * Sets current context
     *
     * @param \Fwk\Core\Context $context Current context
     *
     * @return void
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }


}