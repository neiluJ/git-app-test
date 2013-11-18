<?php
namespace TestGit;

use Symfony\Component\Process\Process;

class UsersService
{
    protected $htpasswdBin;
    
    public function __construct($htpasswdBin)
    {
        $this->htpasswdBin = $htpasswdBin;
    }
    
    public function generateApachePassword($password)
    {
        $proc = new Process(sprintf('%s tmpUsr %s', $this->htpasswdBin, $password));
        $proc->run();
        
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException('unable to generate apache password');
        }
        
        return substr(trim($proc->getOutput()),7 /* tmpUsr: */);
    }
}