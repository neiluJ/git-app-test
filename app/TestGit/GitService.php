<?php
namespace TestGit;

use Symfony\Component\Finder\Finder;
use TestGit\Model\Git\Repository as RepositoryEntity;
use Gitonomy\Git\Repository as GitRepository;

class GitService
{
    protected $repositoriesDir;
    protected $workDir;
    protected $dateFormat;
    
    public function __construct($repositoriesDir, $workDir, $dateFormat = 'd/m/Y')
    {
        if (!is_dir($repositoriesDir)) {
            throw new \Exception('Invalid repositories directory: '. $repositoriesDir);
        }
        
        if (!is_dir($workDir)) {
            throw new \Exception('Invalid working directory: '. $workDir);
        }
        
        $this->repositoriesDir = $repositoriesDir;
        $this->workDir = $workDir;
        $this->dateFormat = $dateFormat;
    }
    
    public function listRepositories()
    {
        $finder = new Finder();
        $result = array();
        
        foreach ($finder->directories()->in($this->repositoriesDir)->depth(0) as $k => $dir) {
            $repo = new \Gitonomy\Git\Repository($dir, array(
                'working_dir' => null
            ));
            
            $headCommit = $repo->getHeadCommit();
            $infos = array(
                'name'  => basename($k),
                'size'  => $repo->getSize(),
                'lastCommit' => array(
                    'message'   => $headCommit->getShortMessage(),
                    'author'    => $headCommit->getAuthorName(),
                    'date'      => $headCommit->getAuthorDate()->format($this->dateFormat),
                    'hash'      => $headCommit->getHash()
                )
            );
            
            array_push($result, $infos);
        }
        return $result;
    }
    
    /**
     *
     * @param RepositoryEntity $repository
     * @return GitRepository 
     */
    public function transform(RepositoryEntity $repository)
    {
        return new GitRepository($this->getRepositoryPath($repository), array(
            'working_dir' => $this->getWorkDirPath($repository)
        ));
    }
    
    public function getRepositoryPath(RepositoryEntity $repository)
    {
        return rtrim($this->repositoriesDir, DIRECTORY_SEPARATOR) . 
                DIRECTORY_SEPARATOR . 
                rtrim($repository->getPath(), DIRECTORY_SEPARATOR) . 
                DIRECTORY_SEPARATOR;
    }
    
    public function getWorkDirPath(RepositoryEntity $repository)
    {
        $repoPath = substr($repository->getPath(), 0, strlen($repository->getPath()) - 4);
        
        return rtrim($this->workDir, DIRECTORY_SEPARATOR) . 
                DIRECTORY_SEPARATOR . 
                $repoPath;
    }
}