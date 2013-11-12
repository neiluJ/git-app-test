<?php
namespace TestGit\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Symfony\Component\Console\Input\InputArgument;
use TestGit\Model\Git\GitDao;
 
class RepoUpdate extends Command implements ServicesAware
{
    protected $services;
    
    protected function configure()
    {
        $this->setDescription('Updates a repository (pulls changes in workdir)');
        $this->addArgument('name',  InputArgument::REQUIRED, 'Repository to be updated');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dao = $this->getGitDao();
        try {
            $repository = $dao->findOne($input->getArgument('name'), GitDao::FIND_FULLNAME);
        } catch(\Exception $exp) {
            $this->getApplication()->renderException($exp, $output);
        }
        
        try {
            $this->getGitService()->updateWorkdir($repository, $output);
        } catch(\RuntimeException $exp) {
            $this->getApplication()->renderException($exp, $output);
        }
        
        $commit = $this->getGitService()->getLastCommit($repository);
        
        $repository->setLast_commit_hash($commit->getHash());
        $repository->setLast_commit_author($commit->getAuthorName());
        $repository->setLast_commit_date($commit->getAuthorDate()->format('Y-m-d H:i:s'));
        $repository->setLast_commit_msg($commit->getShortMessage());
        
        $this->getGitDao()->save($repository);
    }
    
    /**
     * 
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