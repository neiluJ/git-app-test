<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;

class Repository implements ServicesAware
{
    public $name;
    public $branch = 'master';
    public $path;
    
    protected $repository;
    
    protected $services;
    
    protected $files;
    
    public function show()
    {
        try {
            $this->repository = $this->getGitService()->getRepository($this->name);
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        return Result::SUCCESS;
    }
    
    public function tree()
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
        $final = array();
        
        foreach ($tree->getEntries() as $fileName => $infos) {
            $dir = ($infos[0] === '040000' ? true : false);
            $final[($infos[0] === '040000' ? 0 : 1) . $fileName] = array(
                'path'          => $fileName,
                'directory'     => $dir,
                'lastCommit'    => array(
                    'hash'          => $commit->getHash(),
                    'author'        => $commit->getCommitterName(),
                    'date'          => $commit->getCommitterDate()->format('d/m/y'),
                    'message'       => $commit->getShortMessage()
                )
            );
        }
        
        ksort($final);
        $this->files = $final;
        
        return Result::SUCCESS;
    }
    
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Container $services)
    {
        $this->services = $services;
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function getFiles()
    {
        return $this->files;
    }

        /**
     * @return \TestGit\GitService
     */
    protected function getGitService()
    {
        return $this->getServices()->get('git');
    }
}