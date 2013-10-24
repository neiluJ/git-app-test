<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use Fwk\Core\Preparable;

class Repository implements ServicesAware, Preparable
{
    public $name;
    public $branch = 'master';
    public $path;
    
    protected $repository;
    
    protected $services;
    
    protected $files;
    
    public function prepare()
    {
        if (!empty($this->path)) {
            $this->path = rtrim($this->path, '/');
        }
    }
    
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
        
        if (null !== $this->path) {
            $tree = $tree->resolvePath($this->path);
        }
        
        $final = array();
        
        foreach ($tree->getEntries() as $fileName => $infos) {
            $dir = ($infos[0] === '040000' ? true : false);
            $log = $this->repository->getLog($revision, (!empty($this->path) ? ltrim($this->path,'/') . '/' : '') . $fileName, 0, 1);
            $commit = $log->getSingleCommit();
            $final[($infos[0] === '040000' ? 0 : 1) . $fileName] = array(
                'path'          => $fileName,
                'directory'     => $dir,
                'realpath'      => $fileName,
                'special'       => false,
                'lastCommit'    => array(
                    'hash'          => $commit->getHash(),
                    'author'        => $commit->getCommitterName(),
                    'date'          => $commit->getCommitterDate()->format('d/m/y'),
                    'message'       => $commit->getShortMessage(60)
                )
            );
        }
        
        if (!empty($this->path)) {
            $prev = explode('/', $this->path);
            unset($prev[count($prev)-1]);
            
            $final['00'] = array(
                'path'          => '..',
                'directory'     => true,
                'special'       => true,
                'realpath'      => implode('/', $prev),
                'lastCommit'    => array(
                    'hash'          => null,
                    'author'        => null,
                    'date'          => null,
                    'message'       => null
                )
            );
        }
        
        ksort($final);
        $this->files = $final;
        
        return Result::SUCCESS;
    }
    
    public function blob()
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
        
        if (!$tree instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }
        
        $this->blob = $tree;
        
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