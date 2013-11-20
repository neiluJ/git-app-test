<?php
namespace TestGit;

use TestGit\Model\Git\Repository as RepositoryEntity;
use Gitonomy\Git\Repository as GitRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use TestGit\Model\User\User;

class GitService
{
    const UPDATE_LOCK_FILE = 'forgery.update-lock';
    
    protected $repositoriesDir;
    protected $workDir;
    protected $dateFormat;
    protected $gitUsername;
    protected $gitEmail;
    protected $gitFullname;
    
    public function __construct($repositoriesDir, $workDir, $dateFormat, $gitUsername, $gitEmail, $gitFullname)
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
        $this->gitEmail = $gitEmail;
        $this->gitUsername = $gitUsername;
        $this->gitFullname = $gitFullname;
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
        $repoPath = $this->getRepositoryPath($repository);
        if (!is_dir($workDirPath)) {
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $lockFile = rtrim($workDirPath, DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR 
                . self::UPDATE_LOCK_FILE;
        
        // directory is locked for update. 
        // remove the file and stop there
        if (is_file($lockFile)) {
            unlink($lockFile);
            return;
        }
        
        $proc = new Process(sprintf('git --git-dir %s --work-tree . fetch -f -m --all', $repoPath), $workDirPath);
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
    
    public function userConfig(RepositoryEntity $repository)
    {
        $proc = new Process(sprintf('git config user.name "%s" && git config user.email "%s"', $this->gitUsername, $this->gitEmail), $this->getWorkDirPath($repository));
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function add(RepositoryEntity $repository, array $files)
    {
        $final = array();
        foreach ($files as $file) {
            $final[] = $file;
        }
        
        $proc = new Process('git add -f -- '. implode(' ', $final), $this->getWorkDirPath($repository));
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function rm(RepositoryEntity $repository, array $files)
    {
        $final = array();
        foreach ($files as $file) {
            $final[] = $file;
        }
        
        $proc = new Process('git rm -f -- '. implode(' ', $final), $this->getWorkDirPath($repository));
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function commit(RepositoryEntity $repository, User $committer, $message)
    {
        $fn     = $committer->getFullname();
        
        // doing the impersonation commit stuff
        $exec   = sprintf("export GIT_AUTHOR_NAME='%s' ".
            "&& export GIT_AUTHOR_EMAIL='%s' ".
            "&& export GIT_COMMITTER_NAME='%s' ".
            "&& export GIT_COMMITTER_EMAIL='%s' ".
            "&& git commit -m '%s' ".
            "&& unset GIT_AUTHOR_NAME ".
            "&& unset GIT_AUTHOR_EMAIL ".
            "&& unset GIT_COMMITTER_NAME ".
            "&& unset GIT_COMMITTER_EMAIL",
            $this->gitUsername,
            $this->gitEmail,
            (empty($fn) ? $committer->getUsername() : $fn),
            $committer->getEmail(),
            addslashes($message)
        );
        
        $proc = new Process($exec, $this->getWorkDirPath($repository));
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function push(RepositoryEntity $repository)
    {
        $this->userConfig($repository);
        $proc = new Process('git push -f', $this->getWorkDirPath($repository));
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function lockWorkdir(RepositoryEntity $repository)
    {
        $workDirPath = $this->getWorkDirPath($repository);
        if (!is_dir($workDirPath)) {
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $lockFile = rtrim($workDirPath, DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR 
                . self::UPDATE_LOCK_FILE;
        
        if (is_file($lockFile)) {
            return;
        }
        
        file_put_contents($lockFile, 'workdir locked at '. date('Y-m-d H:i:s'));
    }
}