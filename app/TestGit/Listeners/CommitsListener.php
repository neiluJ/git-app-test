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
        $push->setRepositoryId($event->getRepository()->getId());
        
        $username = $event->getUsername();
        if (!empty($username)) {
            $push->setUsername($username);
            try {
                $user = $usersDao->findOne($username);
                $push->setUserId($user->getId());
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
    
    protected function indexCommits(Repository $repository, GitService $git, 
        GitDao $dao, UsersDao $usersDao, &$allReferences
    ) {
        $repo           = $git->transform($repository);
        $new            = $repository->getLast_commit_hash();
        $lastIndexed    = $dao->getLastIndexedCommit($repository);
        $refs           = null;
        
        if (null !== $lastIndexed && $new !== null) {
            $refs = sprintf('%s..%s', $lastIndexed->getHash(), $new);
        }
        
        $log = $repo->getLog($refs);
        if (!$log->countCommits()) {
            return array();
        }
        
        $final = array();
        $commits = array_reverse($log->getCommits());
        foreach ($commits as $repoCommit) {
            $authorName = trim($repoCommit->getAuthorName());
            $authorEmail = trim($repoCommit->getAuthorEmail());
            $committerName = trim($repoCommit->getCommitterName());
            $committerEmail = trim($repoCommit->getCommitterEmail());
            $commit = new Commit();
            $commit->setHash($repoCommit->getHash());
            $commit->setAuthorDate($repoCommit->getAuthorDate()->format('Y-m-d H:i:s'));
            $commit->setAuthorEmail($authorEmail);
            $commit->setAuthorName($authorName);
            $commit->setCommitterDate($repoCommit->getCommitterDate()->format('Y-m-d H:i:s'));
            $commit->setCommitterEmail($committerEmail);
            $commit->setCommitterName($committerName);
            $commit->setIndexDate(date('Y-m-d H:i:s'));
            $commit->setMessage($repoCommit->getMessage());
            $commit->setRepositoryId($repository->getId());
    //            $commit->getPush()->add($this->push);
            
            $user = $this->findUser($authorEmail, $authorName, $usersDao);
            if ($user instanceof User) {
                $commit->setAuthorId($user->getId());
            }

            $user = $this->findUser($committerEmail, $committerName, $usersDao);
            if ($user instanceof User) {
                $commit->setCommitterId($user->getId());
            }
            
            $refs = $this->indexReferences($repository, $repoCommit, $allReferences);
            foreach ($refs as $ref) {
                $allReferences[] = $ref;
            }
            
            $commit->getReferences()->addAll($refs); 
            
            $final[] = $commit;
        }
        
        return $final;
    }
    
    protected function indexTags(Repository $repository, $git, $gitDao, 
        $allReferences
    ) {
        $repo       = $git->transform($repository);
        $refs       = $repo->getReferences();
        $tags       = array();
        
        foreach($refs->getAll() as $ref) {
            if (!$ref instanceof \Gitonomy\Git\Reference\Tag) {
                continue;
            }
            
            $exit = false;
            foreach ($allReferences as $existingRef) {
                if ($existingRef->getName() == $ref->getName()) {
                    $exit = true;
                    break;
                }
            }
            
            if ($exit) {
                continue;
            }
            
            $reference = new Reference();
            $reference->setCreatedOn(date('Y-m-d H:i:s'));
            $reference->setCommitHash($ref->getCommitHash());
            $reference->setName($ref->getName());
            $reference->setRepositoryId($repository->getId());
            $reference->setFullname($ref->getFullname());
            $reference->setType("tag");
            
           // $this->push->getReferences()->add($reference);
            
            $tags[$ref->getName()] = $reference;
        }
        
        return $tags;
    }
    
    protected function indexReferences(Repository $repository, $repoCommit, 
        $allReferences
    ) {
        $references     = $repoCommit->getIncludingBranches(true, false);
        $final          = array();
        
        foreach ($references as $ref) {
            if ($ref instanceof Stash) {
                continue;
            }
            
            $exists = $this->refExists($ref->getFullname(), $allReferences);
            if ($exists !== false) {
                $final[$exists->getName()] = $exists;
                continue;
            }
            
            $reference = new Reference();
            $reference->setCreatedOn(date('Y-m-d H:i:s'));
            $reference->setCommitHash($repoCommit->getHash());
            $reference->setName($ref->getName());
            $reference->setRepositoryId($repository->getId());
            $reference->setFullname($ref->getFullname());
            $reference->setType(($ref instanceof Branch ? 'branch' : 'tag'));
            
           // $this->push->getReferences()->add($reference);
            
            $final[$ref->getName()] = $reference;
        }
        
        return $final;
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