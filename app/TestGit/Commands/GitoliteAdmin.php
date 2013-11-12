<?php
namespace TestGit\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;

class Gitolite extends Command implements ServicesAware
{
    protected $services;
    
    protected function configure()
    {
        $this->setDescription('Prints the to-be-generated gitolite.conf');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dao = $this->getGitDao();
        $repos = $dao->findAll();
        foreach ($repos as $repository) {
            $output->write("repo ". $repository->getFullname(), true); 
            
            $accesses = $repository->getAccesses();
            foreach ($accesses as $access) {
                $output->write("\t" . $access->getGitoliteAccessString() 
                    . "\t=\t" 
                    . $access->getUser()->getUsername(), true);
            }
            
            $output->write("", true);
        }
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
}