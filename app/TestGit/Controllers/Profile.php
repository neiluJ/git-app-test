<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Controller;
use Fwk\Core\Action\Result;
use TestGit\Model\User\User;
use TestGit\Model\User\UsersDao;
use TestGit\Model\Git\GitDao;
use Fwk\Core\Preparable;

class Profile extends Controller implements Preparable
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
    
    public function prepare()
    {
        $this->dateFormat = $this->getServices()->get('git.date.format');
    }

    public function show()
    {
        try {
            $this->loadProfile();
        } catch(\Exception $exception) {
            return Result::ERROR;
        }
        
        $dao = $this->getGitDao();
        $this->repositories = $dao->findMany($this->profile->getId(), GitDao::FIND_OWNER);
        
        $this->getServices()->get('users')->generateApachePassword('test');
        
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
    
    protected function loadProfile()
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
}