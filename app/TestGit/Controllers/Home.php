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
        $this->user = $this->getServices()
                ->get('security')
                ->getAuthenticationManager()
                ->getIdentity();
        
        return Result::SUCCESS;
    }
    
    public function error()
    {
        return Result::SUCCESS;
    }
    
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
}