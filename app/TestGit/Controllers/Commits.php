<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;

class Commits extends Repository 
{
    public $offset;
    public $limit = 25;
    
    protected $commits;
    protected $jsonCommits;
    protected $currentCommit;
    protected $jsonCurrentCommit;
    
    public function prepare()
    {
        parent::prepare();
        
        $this->limit = (int)$this->limit;
        $this->offset = (int)$this->offset;
    }
    
    public function listAction()
    {
        try {
            $this->repository = $this->getGitService()->getRepository($this->name);
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
}