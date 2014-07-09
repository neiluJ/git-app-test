<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Controller;
use Fwk\Core\Action\Result;
use TestGit\Model\User\OrgAccess;
use TestGit\Model\User\User;
use TestGit\Model\User\UsersDao;
use TestGit\Model\Git\GitDao;
use Fwk\Core\Preparable;
use Fwk\Core\ContextAware;
use Fwk\Core\Context;

class Profile extends Repositories implements Preparable, ContextAware
{
    public $username;
    public $target;
    public $right;
    
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
    protected $activityRepositories = array();

    public function prepare()
    {
        $this->dateFormat = $this->getServices()->getProperty('git.date.format');
        $this->target = (int)$this->target;
    }

    public function show()
    {
        try {
            $this->loadProfile();
        } catch(\Exception $exception) {
            $this->errorMsg = $exception;
            return Result::ERROR;
        }

        $dao = $this->getGitDao();
        $this->activityRepositories = $this->loadRepositoriesAcls($dao->findMany($this->profile->getId(), GitDao::FIND_OWNER, 100, false));
        foreach ($this->activityRepositories as $repo) {
            if ($repo->getOwner_id() == $this->profile->getId()) {
                $this->repositories[] = $repo;
            }
        }

        $this->totalCommits = $dao->countCommits($this->profile);

        return Result::SUCCESS;
    }

    public function showActivity()
    {
        try {
            $res = $this->show();
        } catch(\Exception $exception) {
            $this->errorMsg = $exception;
            return Result::ERROR;
        }

        if ($res !== Result::SUCCESS) {
            return Result::ERROR;
        }

        return Result::SUCCESS;
    }

    public function showMembers()
    {
        try {
            $res = $this->show();
        } catch(\Exception $exception) {
            $this->errorMsg = $exception;
            return Result::ERROR;
        }

        if ($res !== Result::SUCCESS) {
            return Result::ERROR;
        }

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

        if ($this->profile->isOrganization() && $user instanceof User) {
            $members = $this->profile->getMembers();
            foreach ($members as $access) {
                if ($user->getId() != $access->getUser_id()) {
                    continue;
                }

                if ((bool)$access->getAdminAccess()) {
                    $acl->allow($user, $this->profile, 'edit');
                }
                if ((bool)$access->getMembersAdminAccess()) {
                    $acl->allow($user, $this->profile, 'edit-members');
                }
                if ((bool)$access->getReposAdminAccess()) {
                    $acl->allow($user, $this->profile, 'repos-admin');
                }
                if ((bool)$access->getReposWriteAccess()) {
                    $acl->allow($user, $this->profile, 'write');
                }
            }
        }

        if (null !== $permission && !$acl->isAllowed($user, $this->profile, $permission)) {
            throw new \RuntimeException('You\'re not allowed to view this page');
        }
    }


    public function toggleOrgUserRight()
    {
        try {
            $this->loadProfile();
        } catch(\Exception $exception) {
            $this->errorMsg = $exception;
            return Result::ERROR;
        }

        if (empty($this->target) || empty($this->right) || !in_array($this->right, array('write', 'members', 'repos', 'admin'))
            || !$this->profile->isOrganization()
        ) {
            $this->errorMsg = "invalid request";
            return Result::ERROR;
        }

        $members = $this->profile->getMembers();
        $target = null;
        foreach ($members as $member) {
            if ($member->getUser_id() == $this->target) {
                $target = $member;
                break;
            }
        }

        if (!$target instanceof OrgAccess) {
            $this->errorMsg = "invalid organization member";
            return Result::ERROR;
        }

        switch($this->right) {
            case 'write':
                $target->setReposWriteAccess(!$target->getReposWriteAccess());
                break;
            case 'members':
                $target->setMembersAdminAccess(!$target->getMembersAdminAccess());
                break;
            case 'repos':
                $target->setReposAdminAccess(!$target->getReposAdminAccess());
                break;
            case 'admin':
                $target->setAdminAccess(!$target->getAdminAccess());
                break;
            default:
                throw new \InvalidArgumentException('this should never happend');
        }

        $dao = $this->getUsersDao();
        $dao->saveOrgAccess($target);

        return Result::SUCCESS;
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

    /**
     * @return array
     */
    public function getActivityRepositories()
    {
        return $this->activityRepositories;
    }
}