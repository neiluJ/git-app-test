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
            $this->getGitService()->installPostReceiveHook($repository, $this->getServices()->get('php.executable'));
        } catch(\Exception $exp) {
            $this->getApplication()->renderException($exp, $output);
            exit(2);
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