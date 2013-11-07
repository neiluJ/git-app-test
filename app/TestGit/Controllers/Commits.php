<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Response;

class Commits extends Repository 
{
    public $offset;
    public $limit = 25;
    public $hash;
    public $compare;
    
    protected $commits;
    protected $jsonCommits;
    protected $currentCommit;
    protected $jsonCurrentCommit;
    protected $diff;
    
    public function prepare()
    {
        parent::prepare();
        
        $this->limit = (int)$this->limit;
        $this->offset = (int)$this->offset;
    }
    
    public function listAction()
    {
        try {
            $this->loadRepository();
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $refs = $this->repository->getReferences();
        if ($refs->hasBranch($this->branch)) {
            $revision = $refs->getBranch($this->branch);
        } else {
            $revision = $this->repository->getRevision($this->branch);
        }
        
        $commit = $revision->getCommit();
        $tree = $commit->getTree();
        
        if (null !== $this->path) {
            $tree = $tree->resolvePath($this->path);
        }
        
        $finalCommits = array();
        $commits = $this->repository->getLog(
            $revision, ltrim($this->path,'/'), $this->offset, $this->limit
        )->getCommits();
        
        foreach ($commits as $commit) {
            $finalCommits[$commit->getHash()] = array(
                'author'    => $commit->getAuthorName(),
                'date'      => $commit->getAuthorDate()->format('d/m/Y H:i:s'),
                'ts'        => $commit->getAuthorDate()->format('U'),
                'date_obj'  => $commit->getAuthorDate(),
                'hash'      => $commit->getHash(),
                'message'   => $commit->getMessage()
            );
        }
        
        $this->commits = $commits;
        $this->jsonCommits = $finalCommits;
        $this->currentCommit = array_shift($commits);
        $this->jsonCurrentCommit = $this->jsonCommits[$this->currentCommit->getHash()];
        
        return Result::SUCCESS;
    }
    
    public function commitAction()
    {
        try {
            $this->loadRepository();
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $revision = $this->repository->getRevision($this->hash);
        
        $commit = $revision->getCommit();
        
        $this->commits = array($commit);
        $this->currentCommit = $commit;
        $this->jsonCommits[$commit->getHash()] = array(
            'author'    => $commit->getAuthorName(),
            'date'      => $commit->getAuthorDate()->format('d/m/Y H:i:s'),
            'ts'        => $commit->getAuthorDate()->format('U'),
            'date_obj'  => $commit->getAuthorDate(),
            'hash'      => $commit->getHash(),
            'message'   => $commit->getMessage()
        );
        $this->jsonCurrentCommit = $this->jsonCommits[$commit->getHash()];
        
        $diff = $this->diff = $commit->getDiff();
        $this->repoAction = 'Commit';
        
        return Result::SUCCESS;
    }
    
    public function compareAction()
    {
        try {
            $this->loadRepository();
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $this->diff = $this->repository->getDiff($this->compare);
        $this->repoAction = 'Compare';
            
        return Result::SUCCESS;
    }
    
    public function diffAction()
    {
        $this->compareAction();
        
        $response   = new Response();
        $response->setExpires(new \DateTime());
        $response->headers->set('Content-Type', 'text/plain');
        
        $response->setContent($this->diff->getRawDiff());
        
        return $response;
    }
    
    public function getCommits()
    {
        return $this->commits;
    }
    
    public function getJsonCommits()
    {
        return $this->jsonCommits;
    }
    
    public function getCurrentCommit() {
        return $this->currentCommit;
    }

    public function getJsonCurrentCommit() {
        return $this->jsonCurrentCommit;
    }
    
    public function getDiff()
    {
        return $this->diff;
    }
}