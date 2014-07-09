<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use TestGit\EmptyRepositoryException;

class Branches extends Repository
{
    protected $branches = array();
    protected $tags     = array();
    
    public function show()
    {
        try {
            $this->loadRepository('read');
        } catch(EmptyRepositoryException $exp) {
            $this->cloneUrlAction();
            return 'empty_repository';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }
        
        $refs = $this->repository->getReferences();
        
        $branches = $tags = array();
        foreach($refs->getAll() as $ref) {
            if ($ref instanceof \Gitonomy\Git\Reference\Branch) {
                $branches[$ref->getCommit()->getAuthorDate()->format('U')] = $ref;
            } else {
                $tags[$ref->getCommit()->getAuthorDate()->format('U')] = $ref;
            }
        }
        
        krsort($branches);
        krsort($tags);
        
        $this->branches = $branches;
        $this->tags     = $tags;
        
        // var_dump($this->branches);
        return Result::SUCCESS;
    }
    
    public function getBranches()
    {
        return $this->branches;
    }
    
    public function getTags()
    {
        return $this->tags;
    }
}