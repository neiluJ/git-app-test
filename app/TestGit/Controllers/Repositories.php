<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\User;

class Repositories implements ServicesAware
{
    protected $repositories = array();
    protected $jsonRepositories = array();
    protected $searchResults = array();
    
    protected $services;
    
    public function show()
    {
        try {
            $this->repositories = $this->loadRepositoriesAcls($this->getGitDao()->findAll());
            
            $this->buildJsonRepositories();
        } catch(\Exception $e) {
            Result::ERROR;
        }
        
        return Result::SUCCESS;
    }
    
    public function search()
    {
        $res = $this->show();
        if ($res == Result::ERROR) {
            return Result::ERROR;
        }
        
        $this->buildSearchResults();
        
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
    
    protected function buildSearchResults()
    {
        $result = array();
        foreach ($this->repositories as $repo) {
            $infos = array(
                'name'  => $repo->getName(),
                'description' => $repo->getDescription(),
                'fork'      => $repo->getParent_id() === null ? false : true,
                'private'   => $repo->isPrivate(),
                'value'  => $repo->getFullname(),
                'tokens'  => array($repo->getFullname(), ($repo->getOwner_id() === null ? null : $repo->getOwner()->getUsername()), $repo->getName()),
                'url'   => $this->getServices()->get('viewHelper')->url('RepositoryNEW', array('name' => $repo->getFullname()))
            );

            array_push($result, $infos);
        }
        
        $this->searchResults = $result;
    }
    
    protected function buildJsonRepositories()
    {
        $result = array();
        foreach ($this->repositories as $repo) {
            $date = new \DateTime($repo->getLast_commit_date());
            $infos = array(
                'name'  => $repo->getName(),
                'ownerName' => ($repo->getOwner_id() === null ? null : $repo->getOwner()->getUsername()),
                'fullname'  => $repo->getFullname(),
                'private'   => $repo->isPrivate(),
                'fork'      => ($repo->getParent_id() === null ? false : true), 
                'size'  => 0,
                'lastCommit' => array(
                    'message'   => $repo->getLast_commit_msg(),
                    'author'    => $repo->getLast_commit_author(),
                    'date'      => $date->format($this->getServices()->getProperty('git.date.format')),
                    'hash'      => $repo->getLast_commit_hash()
                )
            );

            array_push($result, $infos);
        }

        $this->jsonRepositories = $result;
    }
    
    protected function loadRepositoriesAcls($repositories)
    {
        $security   = $this->getServices()->get('security');
        $acl        = $security->getAclManager();
        $final      = array();
        
        foreach ($repositories as $repo) {
            if (!$acl->hasResource($repo)) {
                $acl->addResource($repo, 'repository');
            }
            
            $acl->deny(null, $repo);

            if (!$repo->isPrivate()) {
                $acl->allow(null, $repo, 'read');
            }
        }

        try {
            $user = $security->getUser();
            foreach ($user->getAccesses() as $access) {
                $repository = null;
                foreach ($repositories as $repo) {
                    if ($repo->getId() == $access->getRepository_id()) {
                        $repository = $repo;
                        break;
                    }
                }

                if (null === $repository) {
                    continue;
                }

                if ($repository->getOwner_id() == $user->getId()) {
                    $acl->allow($user, $repository, 'owner');
                }
                
                if ($access->getUser_id() === $user->getId()) {
                    if ($access->getReadAccess()) {
                        $acl->allow($user, $repository, 'read');
                    }
                    if ($access->getWriteAccess()) {
                        $acl->allow($user, $repository, 'write');
                    }
                    if ($access->getSpecialAccess()) {
                        $acl->allow($user, $repository, 'special');
                    }
                    if ($access->getAdminAccess()) {
                        $acl->allow($user, $repository, 'admin');
                    }
                }
            }

            if (isset($this->profile) && $this->profile->isOrganization()) {
                foreach ($repositories as $repo) {
                    if ($this->profile->isOrgMember($user)) {
                        $acl->allow($user, $repo, 'read');
                    }
                }
            }
            
        } catch(\Fwk\Security\Exceptions\AuthenticationRequired $exp) {
            $user = new \Zend\Permissions\Acl\Role\GenericRole('guest');
        }
        
        foreach ($repositories as $repo) {
            if ($acl->isAllowed($user, $repo, 'read')) {
                $final[] = $repo;
            }
        }
        
        return $final;
    }
    
    public function getSearchResults()
    {
        return $this->searchResults;
    }
}