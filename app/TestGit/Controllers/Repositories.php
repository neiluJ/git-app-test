<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;

class Repositories implements ServicesAware
{
    protected $repositories;
    
    protected $services;
    
    public function show()
    {
        try {
            $this->repositories = $this->services->get('git')->listRepositories();
        } catch(\Exception $e) {
            Result::ERROR;
        }
        
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
    
    public function getRepositories()
    {
        return $this->repositories;
    }
}