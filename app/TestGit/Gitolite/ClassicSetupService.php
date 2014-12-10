<?php
namespace TestGit\Gitolite;

use Fwk\Events\Event;
use TestGit\Events\RepositoryEditEvent;
use TestGit\Events\UserAddEvent;
use TestGit\Events\UserSshKeyAddEvent;
use TestGit\Events\UserSshKeyRemoveEvent;
use TestGit\Events\UserChangePasswordEvent;
use TestGit\Events\RepositoryCreateEvent;
use TestGit\Events\RepositoryForkEvent;
use TestGit\Events\RepositoryDeleteEvent;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\UsersDao;
use TestGit\Transactional\Transaction;
use TestGit\Transactional\TransactionException;

/**
 * Class ClassicSetupService
 * This Service is used when gitolite is installed normally (or remotely?).
 * It performs git / ssh actions (ie: VERY SLOW)
 *
 * @package TestGit
 */
class ClassicSetupService
{
    const GITOLITE_ADMIN_REPO   = 'gitolite-admin';
    const GITOLITE_KEYS_DIR     = 'keydir';
    const GITOLITE_CONFIG_FILE  = 'conf/gitolite.conf';
    
    public function onUserChangePassword(UserChangePasswordEvent $event)
    {
        if ($event->getUser()->isOrganization()) {
            return;
        }

        // update .htpasswd file
        $this->generateHtpasswdFile($event);
    }

    public function onUserAdd(UserAddEvent $event)
    {
        if ($event->getUser()->isOrganization()) {
            return;
        }

        // update .htpasswd file
        $this->generateHtpasswdFile($event);
    }

    private function generateHtpasswdFile(Event $event)
    {
        // update .htpasswd file
        $services = $event->getServices();
        $user   = $event->getUser();
        $logger = $services->get('logger');
        $dao    = $services->get('usersDao');
        $users  = $dao->findAll(true);
        $file   = $services->getProperty('apache.htpasswd.file');

        $logger->addInfo(sprintf('[%s/%s] Generating Apache htpasswd file (%s)', $event->getName(), $user->getUsername(), $file));

        if (is_file($file)) {
            if (!is_writable($file)) {
                $logger->addCritical('['. $event->getName() .'] Apache htpasswd file is not writable');
                throw new \RuntimeException('Apache htpasswd file is not writable');
            }
        }

        $contents = "";
        foreach ($users as $usr) {
            if ($usr->isOrganization()) {
                continue;
            }

            $passwd = $usr->getHttp_password();

            // prevent having users without http password
            if (empty($passwd)) {
                continue;
            }

            $contents .= sprintf("%s:%s\n", $usr->getUsername(), $passwd);
        }

        if (empty($contents)) {
            return;
        }

        file_put_contents($file, $contents, LOCK_EX);
        if (!is_file($file) || file_get_contents($file) !== $contents) {
            $logger->addCritical('['. $event->getName() .'] Unable to write Apache htpasswd file (verification failed)');
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
                      ->findOne(self::GITOLITE_ADMIN_REPO, GitDao::FIND_NAME);
        
        $logger->addInfo(sprintf('[onUserSshKeyAdd:%s] User "%s" added a new ssh-key (%s)', $repo->getFullname(), $event->getUser()->getUsername(), $key->title));
        
        if (!$repo instanceof Repository) {
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

        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $key, $repo, $logger) {
            file_put_contents($file, $key->contents, LOCK_EX);
            if (!is_file($file) || file_get_contents($file) !== $key->contents) {
                $logger->addCritical(sprintf('[onUserSshKeyAdd:%s] Unable to write ssh-key "%s" to gitolite (verification failed)', $repo->getFullname(), $file));
                throw new \RuntimeException('unable to write ssh-key to gitolite');
            }
        }, function() use ($file, $logger) {
           if (is_file($file)) {
               unlink($file);
           }
        }, 'file_put_contents()', 'Creating the SSHKey file');

        $tr->add(function() use ($git, $repo) {
            $git->lockWorkdir($repo);
        }, function() use ($git, $repo) {
            $git->unlockWorkdir($repo);
        }, 'LockWorkdir', 'Locking working directory');

        $tr->add(function() use ($git, $repo, $file) {
            $git->add($repo, array($file));
        }, function() use ($git, $repo, $file) {
            $git->rm($repo, array($file));
        }, 'git-add', 'Adding file to git');

        // @todo Find git-undo
        $tr->add(function() use ($git, $repo, $event) {
            $git->commit($repo, $event->getUser(), 'added new ssh-key');
        }, null, 'git-commit', 'Committing');

        // @todo Find git-undo
        $tr->add(function() use ($git, $repo) {
            $git->push($repo);
        }, null, 'git-push', 'Pushing changes');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
        }
    }
    
    public function onUserSshKeyRemove(UserSshKeyRemoveEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";
        $repo       = $event->getServices()->get('gitDao')
                      ->findOne(self::GITOLITE_ADMIN_REPO, GitDao::FIND_NAME);
        
        $logger->addInfo(sprintf('[onUserSshKeyRemove:%s] User "%s" removed a ssh-key (%s)', $repo->getFullname(), $event->getUser()->getUsername(), $key->title));
        
        if (!$repo instanceof Repository) {
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

        $tr = new Transaction($logger);

        $tr->add(function() use ($git, $repo) {
            $git->lockWorkdir($repo);
        }, function() use ($git, $repo) {
            $git->unlockWorkdir($repo);
        }, 'LockWorkdir', 'Locking working directory');

        $tr->add(function() use ($git, $repo, $file) {
            $git->rm($repo, array($file));
        }, function() use ($git, $repo, $file) {
            // @todo Find git-undo
        }, 'git-rm', 'Removing file from git');

        // @todo Find git-undo
        $tr->add(function() use ($git, $repo, $event) {
            $git->commit($repo, $event->getUser(), 'removed ssh-key');
        }, null, 'git-commit', 'Committing');

        // @todo Find git-undo
        $tr->add(function() use ($git, $repo) {
            $git->push($repo);
        }, null, 'git-push', 'Pushing changes');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
        }
    }
    
    public function onRepositoryEdit(RepositoryEditEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $gitDao     = $event->getServices()->get('gitDao');
        $committer  = $event->getCommitter();
        
        $logger->addInfo(sprintf('[RepositoryEditEvent:%s] Repository edited by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $committer->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Repository) {
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
        $owner      = $event->getServices()->get('usersDao')->findOne($repo->getOwner_id(), UsersDao::FIND_ID);
        
        $logger->addInfo(sprintf('[onRepositoryCreate:%s] Repository created by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $owner->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Repository) {
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
        $git->installPostReceiveHook($repo, $event->getServices()->getProperty('php.executable'));
    }
    
    public function onRepositoryFork(RepositoryForkEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $fork       = $event->getFork();
        $gitDao     = $event->getServices()->get('gitDao');
        /** @todo Fix Fwk/Db */
        $owner      = $event->getServices()->get('usersDao')->findOne($fork->getOwner_id(), UsersDao::FIND_ID);
        
        $logger->addInfo(sprintf('[RepositoryForkEvent:%s] Repository created (forked from "%s") by "%s". Generating new gitolite.conf ...', $fork->getFullname(), $repo->getFullname(), $owner->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Repository) {
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
        $git->installPostReceiveHook($fork, $event->getServices()->getProperty('php.executable'));
        $git->push($fork);
    }
    
    public function onRepositoryDelete(RepositoryDeleteEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $gitDao     = $event->getServices()->get('gitDao');
        /** @todo Fix Fwk/Db */
        $owner      = $event->getServices()->get('usersDao')->findOne($repo->getOwner_id(), UsersDao::FIND_ID);
        
        $logger->addInfo(sprintf('[RepositoryDeleteEvent:%s] Repository deleted by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $owner->getFullname()));
        
        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));
        $gitoliteRepo   = $gitDao->findOne(self::GITOLITE_ADMIN_REPO, GitDao::FIND_NAME);
        
        if (!$gitoliteRepo instanceof Repository) {
            $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] gitolite-admin repository not found (?)', $repo->getFullname()));
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($gitoliteRepo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_CONFIG_FILE;
        
        if (!is_file($file)) {
           $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] "%s" not found in gitolite-admin repository (?)', $repo->getFullname(), self::GITOLITE_CONFIG_FILE));
           throw new \RuntimeException('config file not found in gitolite-admin repository');
        } elseif (is_file($file) && !is_writable($file)) {
           $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] "%s" is not writable', $repo->getFullname(), $file));
           throw new \RuntimeException('config file not writable');
        }

        file_put_contents($file, $gitoliteConfig, LOCK_EX);
        
        if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
            $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] Unable to write config to "%s" (verification failed)', $repo->getFullname(), $file));
           throw new \RuntimeException('config file not written (verification failed)');
        }
        
        $git->lockWorkdir($gitoliteRepo);
        $git->add($gitoliteRepo, array($file));
        $git->commit($gitoliteRepo, $owner, 'removed repository '. $repo->getFullname());
        $git->push($gitoliteRepo);
        $git->delete($repo);
    }
    
    protected function getGitoliteConfigAsString(GitDao $gitDao, $forgeryUser)
    {
        $str    = "# This file has been generated by Forgery on ". date('Y-m-d H:i:s') ."\n";
        $str    .= "# It is recommended that you don't edit it manually\n\n";
        
        $repos  = $gitDao->findAll();
        foreach ($repos as $repository) {
            $str .= "repo ". $repository->getGitName() ."\n";
            
            $accesses = $repository->getAccesses();
            foreach ($accesses as $access) {
                $user = $access->getUser()->fetch();

                // is this an organization ?
                if (!$user->isOrganization()) {
                    $str .= "\t" . $access->getGitoliteAccessString()
                        . "\t=\t" . $access->getUser()->getUsername() . "\n";
                    continue;
                }

                // it's an organization, list members and compute (git) access rights
                foreach ($user->getMembers() as $member) {
                    // if the user has a dedicated access to the repo, use it.
                    if (isset($accesses[$member->getUser_id()])) {
                        continue;
                    }

                    $gitoliteAccessStr = '';

                    // members of the organization can read repository
                    if (true === (bool)$access->getReadAccess()) {
                        $gitoliteAccessStr .= "R";
                    }

                    // member has write access to repo ?
                    if (true === (bool)$member->getReposWriteAccess()) {
                        // does the access says so?
                        if (true === (bool)$access->getWriteAccess()) {
                            $gitoliteAccessStr .= "W";
                        }

                        // does the access say W+ ?
                        if (true === (bool)$access->getSpecialAccess()) {
                            $gitoliteAccessStr .= "+";
                        }
                    }

                    $str .= "\t" . $gitoliteAccessStr
                        . "\t=\t" . $member->getUser()->getUsername() . "\n";
                }
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