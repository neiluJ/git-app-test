<?php
namespace TestGit\Listeners;

use TestGit\Events\RepositoryCreateEvent;
use TestGit\Events\RepositoryForkEvent;
use TestGit\Model\User\Activity;

class StaticActivityListener
{
    public function onRepositoryCreate(RepositoryCreateEvent $event)
    {
        $usersDao   = $event->getServices()->get('usersDao');
        $activity   = new Activity();
        $repo       = $event->getRepository();
        
        $activity->setType(Activity::REPO_CREATE);
        $activity->setUserId($repo->getOwner_id());
        $activity->setCreatedOn($repo->getCreated_at());
        $activity->setRepositoryId($repo->getId());
        
        $usersDao->saveUserActivity($activity);
    }
    
    public function onRepositoryFork(RepositoryForkEvent $event)
    {
        $usersDao   = $event->getServices()->get('usersDao');
        $activity   = new Activity();
        $repo       = $event->getRepository();
        $fork       = $event->getFork();
        
        $activity->setType(Activity::REPO_FORK);
        $activity->setUserId($fork->getOwner_id());
        $activity->setCreatedOn($fork->getCreated_at());
        $activity->setRepositoryId($fork->getId());
        
        $activity->setTargetId($repo->getId());
        $activity->setTargetName($repo->getFullname());
        $activity->setTargetUrl($event->getServices()->get('viewHelper')->url('Repository', array('name' => $repo->getFullname())));
        
        $usersDao->saveUserActivity($activity);
    }
}