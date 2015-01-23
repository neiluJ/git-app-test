<?php
namespace TestGit\Listeners;

use TestGit\Events\RepositoryUpdateEvent;
use TestGit\Model\Git\Push;
use TestGit\Model\Git\Commit;
use TestGit\Model\Git\Reference;
use TestGit\Model\Git\Repository;
use TestGit\GitService;
use TestGit\Model\Git\GitDao;
use TestGit\Model\User\UsersDao;
use Gitonomy\Git\Reference\Branch;
use Gitonomy\Git\Reference\Stash;
use TestGit\Model\User\User;

class CommitsListener
{
    protected $references = array();
    protected $push;
    protected $usersCache = array();

    public function onRepositoryUpdate(RepositoryUpdateEvent $event)
    {
        $usersDao   = $event->getServices()->get('usersDao');
        $gitDao     = $event->getServices()->get('gitDao');
        $git        = $event->getServices()->get('git');

        $push = new Push();
        $push->setCreatedOn(date('Y-m-d H:i:s'));
        $push->getRepository()->set($event->getRepository());

        $username = $event->getUsername();
        if (!empty($username)) {
            $push->setUsername($username);
            try {
                $user = $usersDao->findOne($username);
                $push->getAuthor()->set($user);
            } catch(\Exception $exp) {
                $user = null;
            }
        }

        $this->push       = $push;
        $allReferences    = $gitDao->getAllReferences($event->getRepository());
        $commits          = $this->indexCommits($event->getRepository(), $git, $gitDao, $usersDao, $allReferences);
        $tags             = $this->indexTags($event->getRepository(), $git, $gitDao, $allReferences);

        $gitDao->getDb()->beginTransaction();

        $gitDao->savePush($push);

        foreach ($commits as $commit) {
            $commit->setPushId($push->getId());
            foreach ($commit->getReferences() as $ref) {
                if ($ref->getPushId() == null) {
                    $ref->setPushId($push->getId());
                }
            }

            $gitDao->saveCommit($commit);
        }

        foreach ($tags as $tag) {
            $tag->setPushId($push->getId());
            $gitDao->saveReference($tag);
        }

        $gitDao->getDb()->commit();
    }


    protected function refExists($refName, $allReferences)
    {
        foreach ($allReferences as $ref) {
            if ($ref->getFullname() == $refName) {
                return $ref;
            }
        }

        return false;
    }

    protected function findUser($email, $username, UsersDao $usersDao)
    {
        if (!isset($this->usersCache[$email . $username])) {
            $user = null;
            try {
                $user = $usersDao->findOne($email, UsersDao::FIND_EMAIL);
            } catch(\Exception $exp) {
                try {
                    $user = $usersDao->findOne($username, UsersDao::FIND_USERNAME);
                } catch(\Exception $exp) {
                }
            }

            $this->usersCache[$email . $username] = $user;
        }

        return $this->usersCache[$email . $username];
    }
}