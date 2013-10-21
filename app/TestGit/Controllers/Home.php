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
    
    public function show()
    {
        return Result::SUCCESS;
    }
    
    public function menu()
    {
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
}