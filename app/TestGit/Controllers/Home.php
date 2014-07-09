<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;

class Home implements ServicesAware
{
    public $active;
    public $errorMsg;
    
    protected $services;
    protected $user;

    public $entity;
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
}