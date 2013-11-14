<?php
namespace TestGit\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Symfony\Component\Console\Input\InputArgument;
use TestGit\Model\Git\GitDao;
use Symfony\Component\Process\Process;

class RepoInstallHooks extends Command implements ServicesAware
{
    protected $services;
    
    protected function configure()
    {
        $this->setDescription('Installs Forgery hooks on a repository');
        $this->addArgument('name',  InputArgument::REQUIRED, 'Repository name');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dao = $this->getGitDao();
        try {
            $repository = $dao->findOne($input->getArgument('name'), GitDao::FIND_FULLNAME);
        } catch(\Exception $exp) {
            $this->getApplication()->renderException($exp, $output);
            exit(2);
        }
        
        $repoDir = rtrim($this->getGitService()->getRepositoryPath($repository), DIRECTORY_SEPARATOR);
        $postReceiveFilename = $repoDir . 
                DIRECTORY_SEPARATOR . "hooks" . 
                DIRECTORY_SEPARATOR . "post-receive";
        
        if (is_file($postReceiveFilename) && !is_writable($postReceiveFilename)) {
            throw new \RuntimeException(sprintf('File %s is not writable', $postReceiveFilename));
        } 
        
        @file_put_contents($postReceiveFilename, $this->getHookContents($repository), LOCK_EX);
        
        if (!is_file($postReceiveFilename)) {
            throw new \RuntimeException('Unable to write post-receive hook '. $postReceiveFilename);
        }
        
        $proc = new Process(sprintf('chmod +x %s', $postReceiveFilename), $repoDir);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
        
        $output->writeln("post-receive hook successfully installed in ". $postReceiveFilename);
    }
    
    protected function getHookContents(\TestGit\Model\Git\Repository $repository)
    {
        $fullname   = $repository->getFullname();
        $phpExec    = $this->getServices()->get('php.executable');
        $self       = realpath($_SERVER['SCRIPT_FILENAME']);
        
        $str = <<<EOF
#!/bin/sh
$phpExec $self repository:update $fullname 

EOF;
        return $str;
    }
    
    /**
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Container $services)
    {
        $this->services = $services;
    }
    
    /**
     * 
     * @return \TestGit\Model\Git\GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    /**
     * 
     * @return \TestGit\GitService
     */
    protected function getGitService()
    {
        return $this->getServices()->get('git');
    }
}