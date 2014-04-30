<?php
namespace TestGit\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;

class GitoliteAdmin extends Gitolite
{
    
    protected function configure()
    {
        $this->setDescription('Fetches, edit and push changes to the gitolite-admin repo');
        $this->addOption('reason', 'r', InputOption::VALUE_OPTIONAL, 'Why are we updating the configuration?', 'Forgery administration');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetDir = "/home/neiluj/tmp/forgery-gitolite-adm";
        $this->cloneAdminRepository($targetDir, $output);
        $gitoliteConf = $targetDir . DIRECTORY_SEPARATOR . 'conf/gitolite.conf';
        
        if (!is_file($gitoliteConf)) {
            throw new \Exception('conf/gitolite.conf is missing');
        }
        
        $output->write("Editing $gitoliteConf ... ");
        file_put_contents($gitoliteConf, $this->getGitoliteConfigAsString(), LOCK_EX);
        $output->writeln('OK');
        
        $output->write("Adding file to commit-list ... ");
        $proc = new Process(sprintf('git add %s', $gitoliteConf), $targetDir);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
        $output->writeln('OK');
        
        $output->writeln("Configuring users and ssh-keys ... ");
        $users = $this->getUsersDao()->findAll(true);
        foreach ($users as $user) {
            $output->writeln("\t- user: ". $user->getUsername() .": ");
            foreach ($user->getSshKeys() as $key) {
                $filename = $user->getUsername() ."@". $key->title . ".pub";
                $output->writeln("\t    |- sshkey: ". $key->title ." hash: ". $key->hash);
                $file = $targetDir . DIRECTORY_SEPARATOR . "keydir" . DIRECTORY_SEPARATOR .
                        $filename;
                
                if (is_file($file)) {
                    $currentHash = md5_file($file);
                    $output->write("Key $filename already exists: ". $key->title ." / ". md5_file($file) . ": ");
                    
                    if ($currentHash == $key->hash) {
                        $output->writeln("Skipped");
                        continue;
                    } else {
                        $output->writeln("Updating");
                    }
                }
                
                file_put_contents($file, $key->contents, LOCK_EX);
                $proc = new Process(sprintf('git add %s', $file), $targetDir);
                $proc->run();
                if (!$proc->isSuccessful()) {
                    throw new \RuntimeException($proc->getErrorOutput());
                }
            }
        }
        $output->writeln('OK');
        
        $this->commitConfigChanges($targetDir, $input->getOption('reason'), $output);
        // rmdir($workDirPath);
    }
    
    protected function cloneAdminRepository($targetDir, OutputInterface $output)
    {
        $fullClone      = 'git@'. $this->getServices()->getProperty('git.clone.hostname')
                        . ':' . 'gitolite-admin.git';
        
        $output->write("Cloning admin repository... ");
        
        $proc = new Process(sprintf('git clone %s %s', $fullClone, $targetDir));
        $proc->run();
        
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
        
        $output->writeln('OK');
    }
    
    protected function commitConfigChanges($targetDir, $reason, OutputInterface $output)
    {
        $proc = new Process(sprintf('git commit -m "%s"', $reason), $targetDir);
        $proc->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
        
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
    
    /**
     * 
     * @return \TestGit\Model\User\UsersDao
     */
    protected function getUsersDao()
    {
        return $this->getServices()->get('usersDao'); 
    }
}