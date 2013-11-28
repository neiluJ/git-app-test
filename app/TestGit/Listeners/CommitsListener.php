<?php
namespace TestGit\Listeners;

use TestGit\Events\RepositoryUpdateEvent;
use TestGit\Model\Git\Push;
use TestGit\Model\Git\Commit;
use TestGit\Model\Git\Reference;
use TestGit\Model\Git\Repository;
use TestGit\GitService;
use TestGit\Model\Git\GitDao;

class CommitsListener
{
    public function onRepositoryUpdate(RepositoryUpdateEvent $event)
    {
        $usersDao   = $event->getServices()->get('usersDao');
        $gitDao     = $event->getServices()->get('gitDao');
        $git        = $event->getServices()->get('git');
        
        $push = new Push();
        $push->setCreatedOn(date('Y-m-d H:i:s'));
        $push->setRepositoryId($event->getRepository()->getId());
        
        if (isset($_ENV['GL_USER'])) {
            $push->setUsername($_ENV['GL_USER']);
            try {
                $user = $usersDao->findOne($_ENV['GL_USER']);
                $push->setUserId($user->getId());
            } catch(\Exception $exp) {
                $user = null;
            }
        }
        
        $commits = $this->indexCommits($event->getRepository(), $push, $git, $gitDao);
    }
    
    protected function indexCommits(Repository $repository, Push $push, 
        GitService $git, GitDao $dao
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
        foreach ($log->getCommits() as $repoCommit) {
            $commit = new Commit();
            $commit->setHash($repoCommit->getHash());
            $commit->setAuthorDate($repoCommit->getAuthorDate()->format('Y-m-d H:i:s'));
            $commit->setAuthorEmail($repoCommit->getAuthorEmail());
            $commit->setAuthorName($repoCommit->getAuthorName());
            $commit->setCommitterDate($repoCommit->getCommitterDate()->format('Y-m-d H:i:s'));
            $commit->setCommitterEmail($repoCommit->getCommitterEmail());
            $commit->setCommitterName($repoCommit->getCommitterName());
            $commit->setIndexDate(date('Y-m-d H:i:s'));
            $commit->setMessage($repoCommit->getMessage());
            $commit->setPushId($push->getId());
            $commit->setRepositoryId($repository->getId());
            
            $includes = $repoCommit->getIncludingBranches(true, false);
            
            $final[] = $commit;
        }
        
        return $final;
    }
}