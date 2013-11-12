<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use TestGit\Model\Git\Repository;

class Repositories implements ServicesAware
{
    protected $repositories = array();
    protected $jsonRepositories = array();
    
    protected $services;
    
    public function show()
    {
        try {
            $this->repositories = $this->getGitDao()->findAll();
            $this->buildJsonRepositories();
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
    
    public function getJsonRepositories()
    {
        return $this->jsonRepositories;
    }
        
    /**
     * @return \TestGit\Model\Git\GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    protected function buildJsonRepositories()
    {
        $result = array();
        foreach ($this->repositories as $repo) {
            $date = new \DateTime($repo->getLast_commit_date());
            $infos = array(
                'name'  => $repo->getName(),
                'ownerName' => $repo->getOwner()->getUsername(),
                'fullname'  => $repo->getFullname(),
                'size'  => 0,
                'lastCommit' => array(
                    'message'   => $repo->getLast_commit_msg(),
                    'author'    => $repo->getLast_commit_author(),
                    'date'      => $date->format($this->getServices()->get('git.date.format')),
                    'hash'      => $repo->getLast_commit_hash()
                )
            );

            array_push($result, $infos);
        }
        
        $this->jsonRepositories = $result;
    }
}