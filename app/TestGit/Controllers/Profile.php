<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Controller;
use Fwk\Core\Action\Result;
use TestGit\Model\User\User;
use TestGit\Model\User\UsersDao;
use TestGit\Model\Git\GitDao;
use Fwk\Core\Preparable;
use Fwk\Core\ContextAware;
use Fwk\Core\Context;

class Profile extends Repositories implements Preparable, ContextAware
{
    public $username;
    
    /**
     *
     * @var User
     */
    protected $profile;
    protected $avatarUrl;
    protected $repositories = array();
    protected $dateFormat;
    protected $context;
    protected $errorMsg;
    protected $totalCommits;
    
    public function prepare()
    {
        $this->dateFormat = $this->getServices()->get('git.date.format');
    }

    public function show()
    {
        try {
            $this->loadProfile();
        } catch(\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return Result::ERROR;
        }
        
        $dao = $this->getGitDao();
        $this->repositories = $this->loadRepositoriesAcls($dao->findMany($this->profile->getId(), GitDao::FIND_OWNER));
        $this->totalCommits = $dao->countCommits($this->profile);
        
        return Result::SUCCESS;
    }
    
    /**
     *
     * @return UsersDao
     */
    protected function getUsersDao()
    {
        return $this->getServices()->get('usersDao');
    }
    
    /**
     *
     * @return GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    protected function loadProfile($permission = null)
    {
        if (empty($this->username)) {
            throw new \Exception('user not found');
        }
        
        $this->profile = $this->getUsersDao()
                    ->findOne($this->username, UsersDao::FIND_USERNAME, true);
        
        $this->avatarUrl = sprintf(
            "//%s.gravatar.com/avatar/%s?s=80&d=retro",
            $this->getContext()->getRequest()->isSecure() ? "secure" : "www",
            md5($this->profile->getEmail())
        );
        
        // load acls
        $security   = $this->getServices()->get('security');
        $acl        = $security->getAclManager();
        
        if (!$acl->hasResource($this->profile)) {
            $acl->addResource($this->profile, 'user');
        }
        
        if (!$acl->hasRole($this->profile)) {
            $acl->addRole($this->profile, 'user');
        }
        
        $acl->deny(null, $this->profile);
        $acl->allow($this->profile, $this->profile, 'edit');
        
        try {
            $user = $security->getUser();
        } catch(\Fwk\Security\Exceptions\AuthenticationRequired $exp) {
            $user = new \Zend\Permissions\Acl\Role\GenericRole('guest');
         }
         
        if (null !== $permission && !$acl->isAllowed($user, $this->profile, $permission)) {
            throw new \RuntimeException('You\'re not allowed to view this page');
        }
    }

    public function getProfile()
    {
        return $this->profile;
    }
    
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }
    
    public function getRepositories() {
        return $this->repositories;
    }
    
    public function getDateFormat() 
    {
        return $this->dateFormat;
    }
    
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
    
    public function getContext()
    {
        return $this->context;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function getTotalCommits() 
    {
        return $this->totalCommits;
    }
}