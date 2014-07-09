<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Response;
use TestGit\EmptyRepositoryException;
use TestGit\Model\Git\GitDao;

class Commits extends Repository 
{
    public $offset;
    public $limit = 25;
    public $hash;
    public $compare;
    public $q;
    
    protected $commits      = array();
    protected $jsonCommits  = array();
    protected $currentCommit;
    protected $jsonCurrentCommit;
    protected $diff;
    protected $searchResults = array();
    
    public function prepare()
    {
        parent::prepare();
        
        $this->limit    = (int)$this->limit;
        $this->offset   = (int)$this->offset;
        $this->q        = trim((string)$this->q);
    }
    
    public function listAction()
    {
        try {
            $this->loadRepository('read');
        } catch(EmptyRepositoryException $exp) {
            return Result::SUCCESS;
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
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
                'message'   => $commit->getMessage(),
                'comments'  => $this->getServices()->get('comments')->getCommentsCount('commit-'. $this->getEntity()->getId() .'-'. $commit->getHash())
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
            $this->loadRepository('read');
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        $revision = $this->repository->getRevision($this->hash);
        
        $commit = $this->commit = $revision->getCommit();
        
        $this->commits = array($commit);
        $this->currentCommit = $commit;
        $this->jsonCommits[$commit->getHash()] = array(
            'author'    => $commit->getAuthorName(),
            'date'      => $commit->getAuthorDate()->format('d/m/Y H:i:s'),
            'ts'        => $commit->getAuthorDate()->format('U'),
            'date_obj'  => $commit->getAuthorDate(),
            'hash'      => $commit->getHash(),
            'message'   => $commit->getMessage(),
            'comments'  => $this->getServices()->get('comments')->getCommentsCount('commit-'. $this->getEntity()->getId() .'-'. $commit->getHash())
        );
        $this->jsonCurrentCommit = $this->jsonCommits[$commit->getHash()];
        
        $diff = $this->diff = $commit->getDiff();
        $this->repoAction = 'Commit';
        
        return Result::SUCCESS;
    }
    
    public function compareAction()
    {
        try {
            $this->loadRepository('read');
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        if (empty($this->compare)) {
            return Result::SUCCESS;
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
    
    public function search()
    {
        if (empty($this->q) || strlen($this->q) < 3) {
            return Result::SUCCESS;
        }
        
        $gitDao     = $this->getGitDao();
        $security   = $this->getServices()->get('security');
        try {
            $user   = $security->getUser();
        } catch(\Fwk\Security\Exceptions\AuthenticationRequired $exp) {
            $user   = null;
        }
        
        $results    = $gitDao->findCommits($this->q, GitDao::FIND_COMMIT_BOTH, $user);
        $final      = array();
        foreach ($results as $res) {
            $final[] = array(
                'name'      => $res->getHash(),
                'value'     => substr($res->getHash(), 0, 10),
                'committer' => $res->getComputedCommitterName(),
                'date'      => $res->getCommitterDateObj()->format('d/m/Y H:i:s'),
                'repoName'  => $res->getRepository()->getFullname(),
                'shortHash' => substr($res->getHash(), 0, 10),
                'message'   => substr($res->getMessage(), 0, 60) . (strlen($res->getMessage()) > 60 ? '...' : ''),
                'url'       => $this->getServices()->get('viewHelper')->url('CommitNEW', array('name' => $res->getRepository()->getFullname(), 'hash' => $res->getHash()))
            );
        }
        
        $this->searchResults = $final;
        
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
    
    public function getDiff()
    {
        return $this->diff;
    }
    
    public function getSearchResults()
    {
        return $this->searchResults;
    }
}