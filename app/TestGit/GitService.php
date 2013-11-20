<?php
namespace TestGit;

use TestGit\Model\Git\Repository as RepositoryEntity;
use Gitonomy\Git\Repository as GitRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use TestGit\Model\User\User;
use Monolog\Logger;

class GitService
{
    const UPDATE_LOCK_FILE = 'forgery.update-lock';
    
    protected $repositoriesDir;
    protected $workDir;
    protected $dateFormat;
    protected $gitUsername;
    protected $gitEmail;
    protected $gitFullname;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    public function __construct($repositoriesDir, $workDir, $dateFormat, 
        $gitUsername, $gitEmail, $gitFullname, Logger $logger
    ) {
        if (!is_dir($repositoriesDir)) {
            throw new \Exception('Invalid repositories directory: '. $repositoriesDir);
        }
        
        if (!is_dir($workDir)) {
            throw new \Exception('Invalid working directory: '. $workDir);
        }
        
        $this->repositoriesDir  = $repositoriesDir;
        $this->workDir          = $workDir;
        $this->dateFormat       = $dateFormat;
        $this->gitEmail         = $gitEmail;
        $this->gitUsername      = $gitUsername;
        $this->gitFullname      = $gitFullname;
        $this->logger           = $logger;
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
        $this->logger->addDebug('[updateWorkdir/'. $repository->getFullname() .'] updating working directory '. $workDirPath);
        
        $repoPath = $this->getRepositoryPath($repository);
        if (!is_dir($workDirPath)) {
            $this->logger->addError(sprintf("[updateWorkdir/%s] Workdir '%s' is not a directory", $repository->getFullname(), $workDirPath));
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $lockFile = rtrim($workDirPath, DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR 
                . self::UPDATE_LOCK_FILE;
        
        // directory is locked for update. 
        // remove the file and stop there
        if (is_file($lockFile)) {
            $this->logger->addDebug(sprintf("[updateWorkdir/%s] Workdir '%s' locked for update.", $repository->getFullname(), $workDirPath));
            unlink($lockFile);
            return;
        }
        
        $proc = new Process(sprintf('git --git-dir %s --work-tree . fetch -f -m --all', $repoPath), $workDirPath);
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($output, $logger, $repository) {
            if ('err' !== $type) {
                $logger->addDebug('[updateWorkdir/'. $repository->getFullname() .'] git fetch: '. $buffer);
                $output->write($buffer);
            } else {
                $logger->addError('[updateWorkdir/'. $repository->getFullname() .'] git fetch: '. $buffer);
            }
        });
        
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[updateWorkdir/'. $repository->getFullname() .'] git fetch FAIL: '. $proc->getErrorOutput());
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
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            if ('err' !== $type) {
                $logger->addDebug('[userConfig/'. $repository->getFullname() .'] git config: '. $buffer);
            } else {
                $logger->addError('[userConfig/'. $repository->getFullname() .'] git config: '. $buffer);
            }
        });
        
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[userConfig/'. $repository->getFullname() .'] git config FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function add(RepositoryEntity $repository, array $files)
    {
        $this->logger->addDebug('[add/'. $repository->getFullname() .'] adding files: '. implode(', ', $files));
        
        $final = array();
        foreach ($files as $file) {
            $final[] = $file;
        }
        
        $proc = new Process('git add -f -- '. implode(' ', $final), $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            if ('err' !== $type) {
                $logger->addDebug('[add/'. $repository->getFullname() .'] git add: '. $buffer);
            } else {
                $logger->addError('[add/'. $repository->getFullname() .'] git add: '. $buffer);
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[add/'. $repository->getFullname() .'] git add FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function rm(RepositoryEntity $repository, array $files)
    {
        $this->logger->addDebug('[rm/'. $repository->getFullname() .'] removing files: '. implode(', ', $files));
        
        $final = array();
        foreach ($files as $file) {
            $final[] = $file;
        }
        
        $proc = new Process('git rm -f -- '. implode(' ', $final), $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            if ('err' !== $type) {
                $logger->addDebug('[rm/'. $repository->getFullname() .'] git rm: '. $buffer);
            } else {
                $logger->addError('[rm/'. $repository->getFullname() .'] git rm: '. $buffer);
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[rm/'. $repository->getFullname() .'] git rm FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function commit(RepositoryEntity $repository, User $committer, $message)
    {
        $this->logger->addDebug('[commit/'. $repository->getFullname() .'] committing "'. $message .'" by '. $committer->getUsername());
        
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
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            if ('err' !== $type) {
                $logger->addDebug('[commit/'. $repository->getFullname() .'] git commit: '. $buffer);
            } else {
                $logger->addError('[commit/'. $repository->getFullname() .'] git commit: '. $buffer);
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[commit/'. $repository->getFullname() .'] git commit FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function push(RepositoryEntity $repository)
    {
        $this->userConfig($repository);
        $proc = new Process('git push -f', $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            if ('err' !== $type) {
                $logger->addDebug('[push/'. $repository->getFullname() .'] git push: '. $buffer);
            } else {
                $logger->addError('[push/'. $repository->getFullname() .'] git push: '. $buffer);
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[push/'. $repository->getFullname() .'] git push FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function lockWorkdir(RepositoryEntity $repository)
    {
        $workDirPath = $this->getWorkDirPath($repository);
        $this->logger->addDebug('[lockWorkdir/'. $repository->getFullname() .'] locking directory '. $workDirPath);
        
        if (!is_dir($workDirPath)) {
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $lockFile = rtrim($workDirPath, DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR 
                . self::UPDATE_LOCK_FILE;
        
        if (is_file($lockFile)) {
            $this->logger->addDebug('[lockWorkdir/'. $repository->getFullname() .'] directory '. $workDirPath .' was already locked');
            return;
        }
        
        file_put_contents($lockFile, 'workdir locked at '. date('Y-m-d H:i:s'));
    }
}