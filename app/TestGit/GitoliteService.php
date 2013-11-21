<?php
namespace TestGit;

use TestGit\Events\RepositoryEditEvent;
use TestGit\Events\UserSshKeyAddEvent;
use TestGit\Events\UserSshKeyRemoveEvent;
use TestGit\Events\UserChangePasswordEvent;
use TestGit\Events\RepositoryCreateEvent;
use TestGit\Events\RepositoryForkEvent;

class GitoliteService
{
    const GITOLITE_ADMIN_REPO   = 'gitolite-admin';
    const GITOLITE_KEYS_DIR     = 'keydir';
    const GITOLITE_CONFIG_FILE  = 'conf/gitolite.conf';
    
    public function onUserChangePassword(UserChangePasswordEvent $event)
    {
        // update .htpasswd file
        $logger = $event->getServices()->get('logger');
        $dao    = $event->getServices()->get('usersDao');
        $users  = $dao->findAll(true);
        $file   = $event->getServices()->get('apache.htpasswd.file');
        
        $logger->addInfo(sprintf('[onUserChangePassword] User "%s" changed password. Generating Apache htpasswd file (%s)', $event->getUser()->getUsername(), $file));
        
        if (is_file($file)) {
            if (!is_writable($file)) {
                $logger->addCritical('[onUserChangePassword] Apache htpasswd file is not writable');
                throw new \RuntimeException('Apache htpasswd file is not writable');
            }
        }
        
        $contents = "";
        foreach ($users as $usr) {
            $passwd = $usr->getHttp_password();
            
            // prevent having users without http password
            if (empty($passwd)) {
                continue;
            }
            
            /**
             * @todo ---
             * 
             * Add user role "HTTP_GIT" to allow/disallow http access at
             * user level
             */
            $contents .= sprintf("%s:%s\n", $usr->getUsername(), $passwd);
        }
        
        if (empty($contents)) {
            return;
        }
        
        file_put_contents($file, $contents, LOCK_EX);
        if (!is_file($file) || file_get_contents($file) !== $contents) {
            $logger->addCritical('[onUserChangePassword] Unable to write Apache htpasswd file (verification failed)');
            throw new \RuntimeException('unable to write apache htpasswd file');
        }
    }
    
    public function onUserSshKeyAdd(UserSshKeyAddEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";
        $repo       = $event->getServices()->get('gitDao')
                      ->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        $logger->addInfo(sprintf('[onUserSshKeyAdd:%s] User "%s" added a new ssh-key (%s)', $repo->getFullname(), $event->getUser()->getUsername(), $key->title));
        
        if (!$repo instanceof Model\Git\Repository) {
            $logger->addCritical(sprintf('[onUserSshKeyAdd:%s] gitolite-admin repository not found (?)', $repo->getFullname()));
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($repo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_KEYS_DIR . 
                      DIRECTORY_SEPARATOR . $filename;

        if (is_file($file)) {
            $currentHash = md5_file($file);
            if ($currentHash == $key->hash) {
                // key already present
                $logger->addDebug(sprintf('[onUserSshKeyAdd:%s] ssh-key is already present in gitolite-admin', $repo->getFullname()));
                return;
            } 
        }

        file_put_contents($file, $key->contents, LOCK_EX);
        if (!is_file($file) || file_get_contents($file) !== $key->contents) {
            $logger->addCritical(sprintf('[onUserSshKeyAdd:%s] Unable to write ssh-key "%s" to gitolite (verification failed)', $repo->getFullname(), $file));
            throw new \RuntimeException('unable to write ssh-key to gitolite');
        }
        
        $git->lockWorkdir($repo);
        $git->add($repo, array($file));
        $git->commit($repo, $event->getUser(), 'added new ssh-key');
        $git->push($repo);
    }
    
    public function onUserSshKeyRemove(UserSshKeyRemoveEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";
        $repo       = $event->getServices()->get('gitDao')
                      ->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        $logger->addInfo(sprintf('[onUserSshKeyRemove:%s] User "%s" removed a ssh-key (%s)', $repo->getFullname(), $event->getUser()->getUsername(), $key->title));
        
        if (!$repo instanceof Model\Git\Repository) {
            $logger->addCritical(sprintf('[onUserSshKeyRemove:%s] gitolite-admin repository not found (?)', $repo->getFullname()));
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($repo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_KEYS_DIR . 
                      DIRECTORY_SEPARATOR . $filename;

        if (!is_file($file)) {
           $logger->addCritical(sprintf('[onUserSshKeyRemove:%s] Ssh-key "%s" not found in gitolite', $repo->getFullname(), $file));
           throw new \RuntimeException('ssh-key not found in gitolite');
        }

        $git->lockWorkdir($repo);
        $git->rm($repo, array($file));
        $git->commit($repo, $event->getUser(), 'removed ssh-key');
        $git->push($repo);
    }
    
    public function onRepositoryEdit(RepositoryEditEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $gitDao     = $event->getServices()->get('gitDao');
        $committer  = $event->getCommitter();
        
        $logger->addInfo(sprintf('[RepositoryEditEvent:%s] Repository edited by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $committer->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->get('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Model\Git\Repository) {
            $logger->addCritical(sprintf('[RepositoryEditEvent:%s] gitolite-admin repository not found (?)', $repo->getFullname()));
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($gitoliteRepo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_CONFIG_FILE;
        
        if (!is_file($file)) {
           $logger->addCritical(sprintf('[RepositoryEditEvent:%s] "%s" not found in gitolite-admin repository (?)', $repo->getFullname(), self::GITOLITE_CONFIG_FILE));
           throw new \RuntimeException('config file not found in gitolite-admin repository');
        } elseif (is_file($file) && !is_writable($file)) {
           $logger->addCritical(sprintf('[RepositoryEditEvent:%s] "%s" is not writable', $repo->getFullname(), $file));
           throw new \RuntimeException('config file not writable');
        }

        file_put_contents($file, $gitoliteConfig, LOCK_EX);
        
        if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
            $logger->addCritical(sprintf('[RepositoryEditEvent:%s] Unable to write config to "%s" (verification failed)', $repo->getFullname(), $file));
           throw new \RuntimeException('config file not written (verification failed)');
        }
        
        $git->lockWorkdir($gitoliteRepo);
        $git->add($gitoliteRepo, array($file));
        $git->commit($gitoliteRepo, $committer, $event->getReason());
        $git->push($gitoliteRepo);
    }
    
    public function onRepositoryCreate(RepositoryCreateEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $gitDao     = $event->getServices()->get('gitDao');
        /** @todo Fix Fwk/Db */
        $owner      = $event->getServices()->get('usersDao')->findOne($repo->getOwner_id(), Model\User\UsersDao::FIND_ID);
        
        $logger->addInfo(sprintf('[onRepositoryCreate:%s] Repository created by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $owner->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->get('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Model\Git\Repository) {
            $logger->addCritical(sprintf('[onRepositoryCreate:%s] gitolite-admin repository not found (?)', $repo->getFullname()));
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($gitoliteRepo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_CONFIG_FILE;
        
        if (!is_file($file)) {
           $logger->addCritical(sprintf('[RepositoryCreateEvent:%s] "%s" not found in gitolite-admin repository (?)', $repo->getFullname(), self::GITOLITE_CONFIG_FILE));
           throw new \RuntimeException('config file not found in gitolite-admin repository');
        } elseif (is_file($file) && !is_writable($file)) {
           $logger->addCritical(sprintf('[RepositoryCreateEvent:%s] "%s" is not writable', $repo->getFullname(), $file));
           throw new \RuntimeException('config file not writable');
        }

        file_put_contents($file, $gitoliteConfig, LOCK_EX);
        
        if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
            $logger->addCritical(sprintf('[RepositoryCreateEvent:%s] Unable to write config to "%s" (verification failed)', $repo->getFullname(), $file));
           throw new \RuntimeException('config file not written (verification failed)');
        }
        
        $git->lockWorkdir($gitoliteRepo);
        $git->add($gitoliteRepo, array($file));
        $git->commit($gitoliteRepo, $owner, 'created repository '. $repo->getFullname());
        $git->push($gitoliteRepo);
        $git->createWorkdir($repo);
        $git->installPostReceiveHook($repo, $event->getServices()->get('php.executable'));
    }
    
    public function onRepositoryFork(RepositoryForkEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $fork       = $event->getFork();
        $gitDao     = $event->getServices()->get('gitDao');
        /** @todo Fix Fwk/Db */
        $owner      = $event->getServices()->get('usersDao')->findOne($fork->getOwner_id(), Model\User\UsersDao::FIND_ID);
        
        $logger->addInfo(sprintf('[RepositoryForkEvent:%s] Repository created (forked from "%s") by "%s". Generating new gitolite.conf ...', $fork->getFullname(), $repo->getFullname(), $owner->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->get('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Model\Git\Repository) {
            $logger->addCritical(sprintf('[RepositoryForkEvent:%s] gitolite-admin repository not found (?)', $fork->getFullname()));
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($gitoliteRepo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_CONFIG_FILE;
        
        if (!is_file($file)) {
           $logger->addCritical(sprintf('[RepositoryForkEvent:%s] "%s" not found in gitolite-admin repository (?)', $fork->getFullname(), self::GITOLITE_CONFIG_FILE));
           throw new \RuntimeException('config file not found in gitolite-admin repository');
        } elseif (is_file($file) && !is_writable($file)) {
           $logger->addCritical(sprintf('[RepositoryForkEvent:%s] "%s" is not writable', $fork->getFullname(), $file));
           throw new \RuntimeException('config file not writable');
        }

        file_put_contents($file, $gitoliteConfig, LOCK_EX);
        
        if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
            $logger->addCritical(sprintf('[RepositoryForkEvent:%s] Unable to write config to "%s" (verification failed)', $fork->getFullname(), $file));
           throw new \RuntimeException('config file not written (verification failed)');
        }
        
        $git->lockWorkdir($gitoliteRepo);
        $git->add($gitoliteRepo, array($file));
        $git->commit($gitoliteRepo, $owner, 'created fork of '. $repo->getFullname() .' to '. $fork->getFullname());
        $git->push($gitoliteRepo);
        $git->createWorkdir($fork);
        $git->remote($fork, 'add', 'fork', $git->getRepositoryPath($repo));
        $git->pull($fork, 'fork');
        $git->remote($fork, 'rm', 'fork');
        $git->installPostReceiveHook($fork, $event->getServices()->get('php.executable'));
        $git->push($fork);
    }
    
    protected function getGitoliteConfigAsString(Model\Git\GitDao $gitDao, $forgeryUser)
    {
        $str    = "# This file has been generated by Forgery on ". date('Y-m-d H:i:s') ."\n";
        $str    .= "# It is recommended that you don't edit it manually\n\n";
        
        $repos  = $gitDao->findAll();
        foreach ($repos as $repository) {
            $str .= "repo ". $repository->getFullname() ."\n"; 
            
            $accesses = $repository->getAccesses();
            foreach ($accesses as $access) {
                $str .= "\t" . $access->getGitoliteAccessString() 
                    . "\t=\t". $access->getUser()->getUsername() ."\n";
            }
            
            if (!$repository->isPrivate()) {
                $str .= "\tR\t=\t@all\n";
            }
            $str .= "\n";
        }
        
        $str .= "# Forgery needs to access all repositories and 'daemon' is used\n";
        $str .= "# for http access\n";
        $str .= "repo @all\n";
        $str .= sprintf("\tRW+\t=\t%s\n", $forgeryUser);  
        $str .= "\tR\t=\tdaemon\n";
        
        return $str;
    }
}