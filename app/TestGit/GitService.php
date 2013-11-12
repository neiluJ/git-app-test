<?php
namespace TestGit;

use TestGit\Model\Git\Repository as RepositoryEntity;
use Gitonomy\Git\Repository as GitRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
    
    public function updateWorkdir(RepositoryEntity $repository, OutputInterface $output = null)
    {
        $workDirPath = $this->getWorkDirPath($repository);
        if (!is_dir($workDirPath)) {
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $proc = new Process('git pull', $workDirPath);
        $proc->run(function ($type, $buffer) use ($output) {
            if (null === $output) {
                return;
           }
            
            if ('err' !== $type) {
                $output->write($buffer);
            }
        });
        
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function getLastCommit(RepositoryEntity $repository)
    {
        $gitRepo = $this->transform($repository);
        $revision = $gitRepo->getLog(null, null, null, 1);
       
        return $revision->getSingleCommit();
    }
}