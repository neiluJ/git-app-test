<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use Fwk\Core\Preparable;
use TestGit\Model\Git\Repository as RepositoryEntity;

class Repository implements ServicesAware, Preparable
{
    public $name;
    public $branch;
    public $path;
    
    protected $repository;
    protected $entity;
    
    protected $services;
    
    protected $files;
    
    protected $repoAction = 'Repository';
    
    protected $cloneHost;
    
    public function prepare()
    {
        if (!empty($this->path)) {
            $this->path = rtrim($this->path, '/');
        }
        
        $this->cloneHost = $this->getServices()->get('git.clone.hostname');
    }
    
    public function show()
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
        
        $final = array();
        
        foreach ($tree->getEntries() as $fileName => $infos) {
            $dir = ($infos[0] === '040000' ? true : false);
            $log = $this->repository->getLog($revision, (!empty($this->path) ? ltrim($this->path,'/') . '/' : '') . $fileName, 0, 1);
            $commit = $log->getSingleCommit();
            $final[($infos[0] === '040000' ? 0 : 1) . $fileName] = array(
                'path'          => $fileName,
                'directory'     => $dir,
                'realpath'      => (!empty($this->path) ? $this->path . DIRECTORY_SEPARATOR : '') . $fileName,
                'special'       => false,
                'lastCommit'    => array(
                    'hash'          => $commit->getHash(),
                    'author'        => $commit->getCommitterName(),
                    'date'          => $commit->getCommitterDate()->format('d/m/y'),
                    'message'       => $commit->getShortMessage(55)
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
    
    /**
     * @return \TestGit\Model\Git\GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    public function getRepoAction()
    {
        return $this->repoAction;
    }
    
    /**
     *
     */
    protected function loadRepository()
    {
        $this->entity = $this->getGitDao()
                ->findOne($this->name, \TestGit\Model\Git\GitDao::FIND_FULLNAME);
    
        if (!$this->entity instanceof RepositoryEntity) {
            throw new \Exception('repository not found');
        }
        
        $this->repository = $this->getGitService()->transform($this->entity);
        $this->branch = (!isset($this->branch) ? $this->entity->getDefault_branch() : $this->branch);
    }
    
    /**
     *
     * @return RepositoryEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function getCloneHost() {
        return $this->cloneHost;
    }
}