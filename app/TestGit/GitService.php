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
    protected $forgeryUsername;
    protected $forgeryEmail;
    protected $forgeryFullname;
    protected $gitUsername;
    protected $gitCloneHostnameLocal;
    protected $gitExecutable;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    public function __construct($repositoriesDir, $workDir, $gitExecutable, $dateFormat, 
        $forgeryUsername, $forgeryEmail, $forgeryFullname, $gitUsername, 
        $gitCloneHostnameLocal, Logger $logger
    ) {
        if (!is_dir($repositoriesDir)) {
            throw new \Exception('Invalid repositories directory: '. $repositoriesDir);
        }
        
        if (!is_dir($workDir)) {
            throw new \Exception('Invalid working directory: '. $workDir);
        }
        
        $this->repositoriesDir          = $repositoriesDir;
        $this->workDir                  = $workDir;
        $this->gitExecutable            = $gitExecutable;
        $this->dateFormat               = $dateFormat;
        $this->forgeryEmail             = $forgeryEmail;
        $this->forgeryUsername          = $forgeryUsername;
        $this->forgeryFullname          = $forgeryFullname;
        $this->gitCloneHostnameLocal    = $gitCloneHostnameLocal;
        $this->gitUsername              = $gitUsername;
        $this->logger                   = $logger;
    }
    
    /**
     *
     * @param RepositoryEntity $repository
     * @return GitRepository 
     */
    public function transform(RepositoryEntity $repository)
    {
        try {
            $repo = new GitRepository($this->getRepositoryPath($repository), array(
                'working_dir'   => $this->getWorkDirPath($repository),
                'debug'         => true,
                'command'       => $this->gitExecutable
            ));
            $repo->isHeadDetached();
        } catch(\Exception $exp) {
            $msg = $exp->getMessage();
            if (strpos($msg, 'Unable to find HEAD file') !== false) {
                throw new EmptyRepositoryException('repository is empty');
            } else {
                throw $exp;
            }
        }
        
        return $repo;
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
        $this->logger->addDebug('[updateWorkdir:'. $repository->getFullname() .'] updating working directory '. $workDirPath);
        
        $repoPath = $this->getRepositoryPath($repository);
        if (!is_dir($workDirPath)) {
            $this->logger->addError(sprintf("[updateWorkdir:%s] Workdir '%s' is not a directory", $repository->getFullname(), $workDirPath));
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $lockFile = rtrim($workDirPath, DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR 
                . self::UPDATE_LOCK_FILE;
        
        // directory is locked for update. 
        // remove the file and stop there
        if (is_file($lockFile)) {
            $this->logger->addDebug(sprintf("[updateWorkdir:%s] Workdir '%s' locked for update.", $repository->getFullname(), $workDirPath));
            unlink($lockFile);
            return;
        }
        
        $proc = new Process(sprintf('%s --git-dir %s --work-tree . fetch -f -m --all', $this->gitExecutable, $repoPath), $workDirPath);
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($output, $logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[updateWorkdir:'. $repository->getFullname() .'] git fetch: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[updateWorkdir:'. $repository->getFullname() .'] git fetch: '. $line);
                });
            }
        });
        
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[updateWorkdir:'. $repository->getFullname() .'] git fetch FAIL: '. $proc->getErrorOutput());
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
        $proc = new Process(sprintf('%s config user.name "%s" && %s config user.email "%s" && %s config push.default current', $this->gitExecutable, $this->forgeryUsername, $this->gitExecutable, $this->forgeryEmail, $this->gitExecutable), $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[userConfig:'. $repository->getFullname() .'] git config: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[userConfig:'. $repository->getFullname() .'] git config: '. $line);
                });
            }
        });
        
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[userConfig:'. $repository->getFullname() .'] git config FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function add(RepositoryEntity $repository, array $files)
    {
        $this->logger->addDebug('[add:'. $repository->getFullname() .'] adding files: '. implode(', ', $files));
        
        $final = array();
        foreach ($files as $file) {
            $final[] = $file;
        }
        
        $proc = new Process($this->gitExecutable .' add -f -- '. implode(' ', $final), $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[add:'. $repository->getFullname() .'] git add: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[add:'. $repository->getFullname() .'] git add: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[add:'. $repository->getFullname() .'] git add FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function rm(RepositoryEntity $repository, array $files)
    {
        $this->logger->addDebug('[rm:'. $repository->getFullname() .'] removing files: '. implode(', ', $files));
        
        $final = array();
        foreach ($files as $file) {
            $final[] = $file;
        }
        
        $proc = new Process($this->gitExecutable .' rm -f -- '. implode(' ', $final), $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[rm:'. $repository->getFullname() .'] git rm: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[rm:'. $repository->getFullname() .'] git rm: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[rm:'. $repository->getFullname() .'] git rm FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function commit(RepositoryEntity $repository, User $committer, $message)
    {
        $this->logger->addDebug('[commit:'. $repository->getFullname() .'] committing "'. $message .'" by '. $committer->getUsername());
        
        $fn     = $committer->getFullname();
        
        // doing the impersonation commit stuff
        $exec   = sprintf("export GIT_AUTHOR_NAME='%s' ".
            "&& export GIT_AUTHOR_EMAIL='%s' ".
            "&& export GIT_COMMITTER_NAME='%s' ".
            "&& export GIT_COMMITTER_EMAIL='%s' ".
            "&& %s commit -m '%s' ".
            "&& unset GIT_AUTHOR_NAME ".
            "&& unset GIT_AUTHOR_EMAIL ".
            "&& unset GIT_COMMITTER_NAME ".
            "&& unset GIT_COMMITTER_EMAIL",
            $this->forgeryUsername,
            $this->forgeryEmail,
            (empty($fn) ? $committer->getUsername() : $fn),
            $committer->getEmail(),
            $this->gitExecutable,
            addslashes($message)
        );
        
        $proc = new Process($exec, $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[commit:'. $repository->getFullname() .'] git commit: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[commit:'. $repository->getFullname() .'] git commit: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[commit:'. $repository->getFullname() .'] git commit FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function push(RepositoryEntity $repository, $remote = 'origin', $branch = null)
    {
        $this->userConfig($repository);
        if (null === $branch) {
            $branch = $repository->getDefault_branch();
        }
        
        $this->logger->addDebug('[push:'. $repository->getFullname() .'] pushing changes to remote "'. $remote .'" (branch: '. $branch .')');
        
        $proc = new Process($this->gitExecutable .' push -f -u '. $remote .' '. $branch, $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[push:'. $repository->getFullname() .'] git push: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[push:'. $repository->getFullname() .'] git push: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[push:'. $repository->getFullname() .'] git push FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function pull(RepositoryEntity $repository, $remote = 'origin', $branch = null)
    {
        $this->userConfig($repository);
        if (null === $branch) {
            $branch = $repository->getDefault_branch();
        }
        
        $this->logger->addDebug('[pull:'. $repository->getFullname() .'] pulling changes from remote "'. $remote .'" (branch: '. $branch .')');
        
        $proc = new Process($this->gitExecutable .' pull -u '. $remote .' '. $branch, $this->getWorkDirPath($repository));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[pull:'. $repository->getFullname() .'] git pull: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[pull:'. $repository->getFullname() .'] git pull: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[pull:'. $repository->getFullname() .'] git pull FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function remote(RepositoryEntity $repository, $action, $name, $url = null)
    {
        $repoPath = $this->getWorkDirPath($repository);
        
        $this->logger->addDebug('[remote:'. $repository->getFullname() .'] remote '. $action .' '. $name .($url !== null ? ' ('. $url .')' : null));
        
        $proc = new Process(sprintf('%s remote %s %s %s', $this->gitExecutable, $action, $name, $url), $repoPath);
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[remote:'. $repository->getFullname() .'] git remote '. $action .': '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[remote:'. $repository->getFullname() .'] git remote '. $action .': ' . $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[remoteAdd:'. $repository->getFullname() .'] git remote FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function lockWorkdir(RepositoryEntity $repository)
    {
        $workDirPath = $this->getWorkDirPath($repository);
        $this->logger->addDebug('[lockWorkdir:'. $repository->getFullname() .'] locking directory '. $workDirPath);
        
        if (!is_dir($workDirPath)) {
            throw new \Exception(sprintf("Workdir '%s' is not a directory", $workDirPath));
        }
        
        $lockFile = rtrim($workDirPath, DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR 
                . self::UPDATE_LOCK_FILE;
        
        if (is_file($lockFile)) {
            $this->logger->addDebug('[lockWorkdir:'. $repository->getFullname() .'] directory '. $workDirPath .' was already locked');
            return;
        }
        
        file_put_contents($lockFile, 'workdir locked at '. date('Y-m-d H:i:s'));
    }
    
    public function createWorkdir(RepositoryEntity $repository)
    {
        $workDirPath = $this->getWorkDirPath($repository);
        $this->logger->addDebug('[createWorkdir:'. $repository->getFullname() .'] creating work directory '. $workDirPath);
        
        if (is_dir($workDirPath)) {
            throw new \Exception(sprintf("Workdir '%s' already exists", $workDirPath));
        }
        
        $proc = new Process(sprintf('%s clone %s@%s:%s %s', $this->gitExecutable, $this->gitUsername, $this->gitCloneHostnameLocal, $repository->getPath(), $this->getWorkDirPath($repository)));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[createWorkdir:'. $repository->getFullname() .'] git clone: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[createWorkdir:'. $repository->getFullname() .'] git clone: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[createWorkdir:'. $repository->getFullname() .'] git clone FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    public function installPostReceiveHook(RepositoryEntity $repository, 
        $phpExecutable = '/usr/bin/php'
    ) {
        $this->logger->addDebug('[installPostReceiveHook:'. $repository->getFullname() .'] adding post-receive hook ...');
        $repoDir = rtrim($this->getRepositoryPath($repository), DIRECTORY_SEPARATOR);
        $postReceiveFilename = $repoDir . 
                DIRECTORY_SEPARATOR . "hooks" . 
                DIRECTORY_SEPARATOR . "post-receive";
        
        if (is_file($postReceiveFilename) && !is_writable($postReceiveFilename)) {
            $this->logger->addCritical('[installPostReceiveHook:'. $repository->getFullname() .'] post-receive hook file "'. $postReceiveFilename .'" is not writable');
            throw new \RuntimeException(sprintf('File %s is not writable', $postReceiveFilename));
        } 
        
        $hookContents = $this->getHookContents($repository, $phpExecutable);
        file_put_contents($postReceiveFilename, $hookContents, LOCK_EX);
        
        if (!is_file($postReceiveFilename) 
            || file_get_contents($postReceiveFilename) !== $hookContents
        ) {
            $this->logger->addCritical('[installPostReceiveHook:'. $repository->getFullname() .'] cannot write post-receive hook "'. $postReceiveFilename .'" (verification failed)');
            throw new \RuntimeException('Unable to write post-receive hook (verification failed)');
        }
        
        $proc = new Process(sprintf('chmod +x %s', $postReceiveFilename), $repoDir);
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[installPostReceiveHook:'. $repository->getFullname() .'] chmod: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[installPostReceiveHook:'. $repository->getFullname() .'] chmod: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[installPostReceiveHook:'. $repository->getFullname() .'] chmod FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    
    protected function getHookContents(RepositoryEntity $repository, 
        $phpExecutable = '/usr/bin/php'
    ) {
        $fullname   = $repository->getFullname();
        $self       = realpath($_SERVER['SCRIPT_FILENAME']);
        
        $str = <<<EOF
#!/bin/sh
$phpExecutable $self repository:update $fullname 

EOF;
        return $str;
    }
    
    public function isEmpty(RepositoryEntity $repository)
    {
        $proc = new Process(sprintf('/usr/bin/find objects -type f | /usr/bin/wc -l'), $this->getRepositoryPath($repository));
        $proc->run();
        if (!$proc->isSuccessful()) {
            $this->logger->addCritical('[isEmpty:'. $repository->getFullname() .'] find FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
        
        return (int)$proc->getOutput() === 0;
    }
    
    public function delete(RepositoryEntity $repository)
    {
        $workDirPath = $this->getWorkDirPath($repository);
        $repoDirPath = $this->getRepositoryPath($repository);
        
        $this->logger->addDebug('[delete:'. $repository->getFullname() .'] deleting repository (and workdir)');
        
        $proc = new Process(sprintf('/bin/rm -Rf %s %s', $workDirPath, $repoDirPath));
        $logger = $this->logger;
        $proc->run(function ($type, $buffer) use ($logger, $repository) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));
            
            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addDebug('[delete:'. $repository->getFullname() .'] rm: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $repository) {
                    $logger->addError('[delete:'. $repository->getFullname() .'] rm: '. $line);
                });
            }
        });
        if (!$proc->isSuccessful()) {
            $logger->addCritical('[delete:'. $repository->getFullname() .'] rm FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
}