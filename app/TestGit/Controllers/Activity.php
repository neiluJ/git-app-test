<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Controller;
use TestGit\Model\Git\GitDao;
use Fwk\Core\Action\Result;
use TestGit\Model\User\User;
use TestGit\Model\User\UsersDao;
use TestGit\Model\User\Activity as ActivityModel;

class Activity extends Controller
{
    protected $repositories = array();
    protected $user;
    protected $errorMsg;
    protected $activities = array();
    
    public function show()
    {
        if (!count($this->repositories)) {
            return Result::SUCCESS;
        }
        
        $pushes = $this->getGitDao()->getActivity($this->repositories, $this->user);
        $statics = $this->getUsersDao()->getUserActivity($this->repositories, $this->user);
        
        $activities = array();
        foreach ($pushes as $idx => $push) {
            $repository = null;
            foreach ($this->repositories as $repo) {
                if ($repo->getId() == $push->getRepositoryId()) {
                    $repository = $repo;
                    break;
                }
            }
            
            $commits = $push->getCommits();
            $references = $push->getReferences();
            
            if (count($commits)) {
                $activity = new \stdClass();
                $activity->type = ActivityModel::DYN_PUSH;
                $activity->repository = $repository;
                $activity->user = ($push->getUserId() != null ? $push->getAuthor() : null);
                $activity->commits = array();
                $activity->username = ($push->getUsername() != null ? $push->getUsername() : 'Anonymous');
                $activity->date = new \DateTime($push->getCreatedOn());

                foreach ($commits as $commit) {
                    $activity->commits[$commit->getCommitterDateObj()->format('U')] = $commit;
                }

                krsort($activity->commits);
                $activities[$activity->date->format('U') . $idx] = $activity;
            }
            
            foreach ($references as $idx => $ref) {
                if ($ref->getPushId() != $push->getId()) {
                    continue;
                }
                
                $activity = new \stdClass();
                $activity->type = ActivityModel::DYN_REF_CREATE;
                $activity->reference = $ref;
                $activity->repository = $repository;
                $activity->user = ($push->getUserId() != null ? $push->getAuthor() : null);
                $activity->username = ($push->getUsername() != null ? $push->getUsername() : 'Anonymous');
                $activity->date = new \DateTime($push->getCreatedOn());
                
                $activities[$activity->date->format('U') . $idx] = $activity;
            }
        }
        
        foreach ($statics as $idx => $activitiy) {
            $repository = null;
            foreach ($this->repositories as $repo) {
                if ($repo->getId() == $activitiy->getRepositoryId()) {
                    $repository = $repo;
                    break;
                }
            }
            
            $activity = new \stdClass();
            $activity->type = $activitiy->getType();
            $activity->obj = $activitiy;
            
            if ($repository !== null) {
                $activity->repository = $repository;
            }
            
            $activity->user = ($activitiy->getUserId() != null ? $activitiy->getUser() : null);
            $activity->date = new \DateTime($activitiy->getCreatedOn());
            $activity->message = $activitiy->getMessage();
            $activities[$activity->date->format('U') . $idx] = $activity;
        }
        
        krsort($activities);
        
        $this->activities = $activities;
        
        return Result::SUCCESS;
    }
    
    /**
     * 
     * @return GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    public function getRepositories()
    {
        return $this->repositories;
    }

    public function setRepositories(array $repositories)
    {
        $this->repositories = $repositories;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
    
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
    
    public function getActivities()
    {
        return $this->activities;
    }
    
    /**
     * 
     * @return UsersDao
     */
    public function getUsersDao()
    {
        return $this->getServices()->get('usersDao');
    }
}