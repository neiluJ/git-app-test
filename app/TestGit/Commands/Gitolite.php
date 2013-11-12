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
        $output->writeln($this->getGitoliteConfigAsString());
    }
    
    protected function getGitoliteConfigAsString()
    {
        $str    = "";
        $dao    = $this->getGitDao();
        $repos  = $dao->findAll();
        
        foreach ($repos as $repository) {
            $str .= "repo ". $repository->getFullname() ."\n"; 
            
            $accesses = $repository->getAccesses();
            foreach ($accesses as $access) {
                $str .= "\t" . $access->getGitoliteAccessString() 
                    . "\t=\t" 
                    . $access->getUser()->getUsername() ."\n";
            }
            
            if ((bool)$repository->getHttp_access()) {
                $str .= "\tR\t=\tdaemon\n";
            }
            
            $str .= "\n";
        }
        
        return $str;
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