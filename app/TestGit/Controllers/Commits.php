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

    public $year;
    public $month;

    public $repo;
    
    protected $commits      = array();
    protected $jsonCommits  = array();
    protected $currentCommit;
    protected $jsonCurrentCommit;
    protected $diff;
    protected $searchResults = array();

    protected $totalCommits = 0;
    protected $monthlyCount = array();
    
    public function prepare()
    {
        parent::prepare();
        
        $this->limit    = (int)$this->limit;
        $this->offset   = (int)$this->offset;
        $this->q        = trim((string)$this->q);

        $this->year     = (int)$this->year;
        $this->month    = (int)$this->month;

        if (empty($this->year)) {
            $this->year = (int)date('Y');
        }

        if (empty($this->month)) {
            $this->month = (int)date('m');
        }

        $this->repoAction = 'CommitsNEW';
    }
    
    public function listAction()
    {
        try {
            $this->loadRepository('read');
            $refs = $this->repository->getReferences();
            if ($refs->hasBranch($this->branch)) {
                $revision = $refs->getBranch($this->branch);
            } else {
                $revision = $this->repository->getRevision($this->branch);
            }

            // tests the reference
            $revision->getCommit();

            $finalCommits = array();
            $this->totalCommits = $this->getGitDao()->getTotalCommitsCount($this->entity);
            $this->monthlyCount = $this->getGitDao()->getCommitsMonthlyCount($this->entity);
            krsort($this->monthlyCount);
            $commits = $this->getGitDao()->getMonthCommits($this->entity, $this->year, $this->month);

        } catch(EmptyRepositoryException $exp) {
            return Result::SUCCESS;
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }
        
        foreach ($commits as $commit) {
            $date = new \DateTime($commit->getCommitterDate());
            $finalCommits[$commit->getHash()] = array(
                'author'    => $commit->getAuthorName(),
                'date'      => $date->format('d/m/Y H:i:s'),
                'ts'        => $date->format('U'),
                'date_obj'  => $date,
                'hash'      => $commit->getHash(),
                'message'   => $commit->getMessage(),
                'comments'  => $this->getServices()->get('comments')->getCommentsCount('commit-'. $this->getEntity()->getId() .'-'. $commit->getHash())
            );
        }
        
        $this->commits = $commits;
        $this->jsonCommits = $finalCommits;

        return Result::SUCCESS;
    }
    
    public function commitAction()
    {
        try {
            $this->loadRepository('read');
            $revision = $this->repository->getRevision($this->hash);
            $commit = $this->commit = $revision->getCommit();
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }
        
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
        $this->repoAction = 'CommitsNEW';
        
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
        
        $results    = $gitDao->findCommits($this->q, GitDao::FIND_COMMIT_BOTH, $user, $this->repo);
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

    /**
     * @return array
     */
    public function getMonthlyCount()
    {
        return $this->monthlyCount;
    }

    /**
     * @return int
     */
    public function getTotalCommits()
    {
        return $this->totalCommits;
    }
}