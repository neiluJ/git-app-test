<?php
namespace TestGit;

use Symfony\Component\Finder\Finder;

class GitService
{
    protected $repositoriesDir;
    protected $dateFormat;
    
    public function __construct($repositoriesDir, $dateFormat = 'd/m/Y')
    {
        if (!is_dir($repositoriesDir)) {
            throw new \Exception('Invalid directory: '. $repositoriesDir);
        }
        
        $this->repositoriesDir = $repositoriesDir;
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
     * @param string $repoName
     * @return \Gitonomy\Git\Repository
     */
    public function getRepository($repoName)
    {
        return new \Gitonomy\Git\Repository($this->repositoriesDir . DIRECTORY_SEPARATOR . $repoName, array(
            'working_dir' => null
        ));
    }
}