<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;

class Users implements ServicesAware
{
    protected $users = array();
    
    protected $services;
    
    public function show()
    {
        $db = $this->getServices()->get('usersDao');
        var_dump($db);
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
    
    public function getUsers()
    {
        return $this->users;
    }
}