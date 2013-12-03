<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Controller;
use TestGit\Model\Git\GitDao;
use Fwk\Core\Action\Result;
use TestGit\Model\User\User;

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
        
        $activities = array();
        foreach ($pushes as $push) {
            $repository = null;
            foreach ($this->repositories as $repo) {
                if ($repo->getId() == $push->getRepositoryId()) {
                    $repository = $repo;
                    break;
                }
            }
            
            $commits = $push->getCommits();
            $references = $push->getReferences();
            
            $activity = new \stdClass();
            $activity->type = "push";
            $activity->repository = $repository;
            $activity->user = ($push->getUserId() != null ? $push->getAuthor() : null);
            $activity->commits = array();
            $activity->username = ($push->getUsername() != null ? $push->getUsername() : 'Anonymous');
            $activity->date = new \DateTime($push->getCreatedOn());
            
            foreach ($commits as $commit) {
                $activity->commits[$commit->getCommitterDateObj()->format('U')] = $commit;
            }
            
            krsort($activity->commits);
            $activities[] = $activity;
            
            foreach ($references as $ref) {
                if ($ref->getPushId() != $push->getId()) {
                    continue;
                }
                
                $activity = new \stdClass();
                $activity->type = "new-ref";
                $activity->reference = $ref;
                $activity->repository = $repository;
                $activity->user = ($push->getUserId() != null ? $push->getAuthor() : null);
                $activity->username = ($push->getUsername() != null ? $push->getUsername() : 'Anonymous');
                $activity->date = new \DateTime($push->getCreatedOn());
                
                $activities[] = $activity;
            }
        }
        
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
}