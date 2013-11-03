<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;

class Users implements ServicesAware
{
    protected $users = array();
    protected $jsonUsers = array();
    
    protected $services;
    
    public function show()
    {
        $this->users = $this->getUsersDao()->findAll(false);
        
        $final = array();
        foreach ($this->users as $user) {
            $final[$user->getId()] = array(
                'id'        => $user->getId(),
                'username'  => $user->getUsername(),
                'emails'    => array($user->getEmail()),
                'fullname'  => $user->getFullname(),
                'added_date'     => $user->getDate_registration(),
                'active'    => $user->getActive()
            );
        }
        $this->jsonUsers = $final;
        
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
    
    public function getJsonUsers()
    {
        return $this->jsonUsers;
    }
    
    /**
     * 
     * @return \TestGit\Model\User\Dao
     */
    protected function getUsersDao()
    {
        return $this->getServices()->get('usersDao');
    }
}