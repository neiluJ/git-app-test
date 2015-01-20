<?php
namespace TestGit\Gitolite;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use TestGit\Events\RepositoryCreateEvent;
use TestGit\Events\RepositoryDeleteEvent;
use TestGit\Events\RepositoryEditEvent;
use TestGit\Events\RepositoryForkEvent;
use TestGit\Events\UserSshKeyAddEvent;
use TestGit\Events\UserSshKeyRemoveEvent;
use TestGit\Model\User\UsersDao;
use TestGit\Transactional\Transaction;
use TestGit\Transactional\TransactionException;

/**
 * Class ServersideSetupService
 * This service is used when gitolite is installed with gitolite setup -a dummy
 *
 * @see http://gitolite.com/gitolite/odds-and-ends.html#server-side-admin
 *
 * @package TestGit
 */
class ServersideSetupService extends ClassicSetupService
{
    const GITOLITE_PATH = '.gitolite';
    const GITOLITE_BIN  = 'gitolite';
    const GIT_HOME      = '/home/git';

    protected $gitolitePath;
    protected $gitoliteBin;
    protected $gitHome;

    public function __construct($gitolitePath = "", $gitoliteBin = self::GITOLITE_BIN, $gitHome = self::GIT_HOME)
    {
        if (empty($gitolitePath)) {
            if (isset($_SERVER['GITOLITE_HTTP_HOME'])) {
                $gitolitePath = $_SERVER['GITOLITE_HTTP_HOME'] . DIRECTORY_SEPARATOR;
            }
            $gitolitePath .= self::GITOLITE_PATH;
        }

        $this->gitolitePath = $gitolitePath;
        $this->gitoliteBin  = $gitoliteBin;
        $this->gitHome      = $gitHome;
    }

    public function onUserSshKeyAdd(UserSshKeyAddEvent $event)
    {
        // Gitolite doc:
        //
        // gets all the keys and dumps them into $HOME/.gitolite/keydir (or into a subdirectory of it), and
        // runs gitolite trigger SSH_AUTHKEYS.
        $logger     = $event->getServices()->get('logger');
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";

        $logger->addInfo(sprintf('[onUserSshKeyAdd] User "%s" added a new ssh-key (%s)', $event->getUser()->getUsername(), $key->title));

        if (!is_dir($this->gitolitePath)) {
            $logger->addCritical(sprintf('[onUserSshKeyAdd] gitolite path (%s) not found (?)', $this->gitolitePath));
            throw new \RuntimeException('gitolite path not found (?)');
        }

        $file       = rtrim($this->gitolitePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . ClassicSetupService::GITOLITE_KEYS_DIR .
            DIRECTORY_SEPARATOR . $filename;

        if (is_file($file)) {
            $currentHash = md5_file($file);
            if ($currentHash == $key->hash) {
                // key already present
                $logger->addDebug(sprintf('[onUserSshKeyAdd] ssh-key already exists'));
                return;
            }
        }

        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $key, $logger) {
            file_put_contents($file, $key->contents, LOCK_EX);
            if (!is_file($file) || file_get_contents($file) !== $key->contents) {
                $logger->addCritical(sprintf('[onUserSshKeyAdd] Unable to write ssh-key "%s" to gitolite (write failed)', $file));
                throw new \RuntimeException('unable to write ssh-key to gitolite');
            }
        }, function() use ($file, $logger) {
            if (is_file($file)) {
                unlink($file);
            }
        }, 'file_put_contents()', 'Creating the SSHKey file');

        $tr->add(function() use ($logger) {
            $this->runGitolite("trigger SSH_AUTHKEYS", $logger);
        }, null, 'gitolite-trigger', 'Refreshing gitolite SSH_AUTHKEYS');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
            throw $exp;
        }
    }

    public function onUserSshKeyRemove(UserSshKeyRemoveEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";

        $logger->addInfo(sprintf('[onUserSshKeyRemove] User "%s" removed a key (%s)', $userName, $key->title));

        if (!is_dir($this->gitolitePath)) {
            $logger->addCritical(sprintf('[onUserSshKeyRemove] gitolite path (%s) not found (?)', $this->gitolitePath));
            throw new \RuntimeException('gitolite path not found (?)');
        }

        $file       = rtrim($this->gitolitePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . ClassicSetupService::GITOLITE_KEYS_DIR .
            DIRECTORY_SEPARATOR . $filename;

        if (!is_file($file)) {
            $logger->addCritical(sprintf('[onUserSshKeyRemove:%s] Ssh-key "%s" not found in gitolite', $userName, $file));
            throw new \RuntimeException('ssh-key not found in gitolite');
        }

        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $key, $logger, $userName) {
            unlink($file);
            if (is_file($file)) {
                $logger->addCritical(sprintf('[onUserSshKeyRemove:%s] Unable to delete ssh-key "%s" from gitolite (write failed)', $userName, $file));
                throw new \RuntimeException('unable to write ssh-key to gitolite');
            }
        }, function() use ($file, $key, $logger) {
            file_put_contents($file, $key->contents, LOCK_EX);
        }, 'unlink()', 'Removing SSHKey file');

        $tr->add(function() use ($logger) {
            $this->runGitolite("trigger SSH_AUTHKEYS", $logger);
        }, null, 'gitolite-trigger', 'Refreshing gitolite SSH_AUTHKEYS');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
            throw $exp;
        }
    }

    public function onRepositoryEdit(RepositoryEditEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $gitDao     = $event->getServices()->get('gitDao');
        $committer  = $event->getCommitter();

        $logger->addInfo(sprintf('[onRepositoryEdit:%s] Repository edited by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $committer->getFullname()));

        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));

        $file       = rtrim($this->gitolitePath, DIRECTORY_SEPARATOR)  .
            DIRECTORY_SEPARATOR . ClassicSetupService::GITOLITE_CONFIG_FILE;

        if (!is_file($file)) {
            $logger->addCritical(sprintf('[onRepositoryEdit:%s] "%s" not found in gitolite path "%s" (?)', $repo->getFullname(), ClassicSetupService::GITOLITE_CONFIG_FILE, $this->gitolitePath));
            throw new \RuntimeException('config file not found in gitolite');
        } elseif (is_file($file) && !is_writable($file)) {
            $logger->addCritical(sprintf('[onRepositoryEdit:%s] "%s" is not writable', $repo->getFullname(), $file));
            throw new \RuntimeException('config file not writable');
        }

        $backup = file_get_contents($file);

        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $logger, $gitoliteConfig, $repo) {
            file_put_contents($file, $gitoliteConfig, LOCK_EX);
            if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
                $logger->addCritical(sprintf('[onRepositoryEdit:%s] Unable to write config to "%s" (write failed)', $repo->getFullname(), $file));
                throw new \RuntimeException('config file not written (write failed)');
            }
        }, function() use ($file, $backup, $logger) {
            file_put_contents($file, $backup, LOCK_EX);
        }, 'write-config', 'Writing gitolite.conf');

        $tr->add(function() use ($logger) {
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, null, 'gitolite-trigger', 'Refreshing gitolite');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
            throw $exp;
        }
    }

    public function onRepositoryCreate(RepositoryCreateEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $gitDao     = $event->getServices()->get('gitDao');
        $owner      = $event->getServices()->get('usersDao')->findOne($repo->getOwner_id(), UsersDao::FIND_ID);
        $git        = $event->getServices()->get('git');

        $logger->addInfo(sprintf('[onRepositoryCreate:%s] Repository created by "%s". Generating new gitolite.conf ...', $repo->getFullname(), $owner->getFullname()));

        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));

        $file       = rtrim($this->gitolitePath, DIRECTORY_SEPARATOR)  .
            DIRECTORY_SEPARATOR . ClassicSetupService::GITOLITE_CONFIG_FILE;

        if (!is_file($file)) {
            $logger->addCritical(sprintf('[RepositoryCreateEvent:%s] "%s" not found in gitolite path "%s" (?)', $repo->getFullname(), ClassicSetupService::GITOLITE_CONFIG_FILE, $this->gitolitePath));
            throw new \RuntimeException('config file not found in gitolite');
        } elseif (is_file($file) && !is_writable($file)) {
            $logger->addCritical(sprintf('[RepositoryCreateEvent:%s] "%s" is not writable', $repo->getFullname(), $file));
            throw new \RuntimeException('config file not writable');
        }

        $backup = file_get_contents($file);

        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $logger, $gitoliteConfig, $repo) {
            file_put_contents($file, $gitoliteConfig, LOCK_EX);
            if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
                $logger->addCritical(sprintf('[RepositoryCreateEvent:%s] Unable to write config to "%s" (write failed)', $repo->getFullname(), $file));
                throw new \RuntimeException('config file not written (write failed)');
            }
        }, function() use ($file, $backup, $logger) {
            file_put_contents($file, $backup, LOCK_EX);
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, 'write-config', 'Writing gitolite.conf');

        $tr->add(function() use ($logger) {
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, null, 'gitolite-trigger', 'Refreshing gitolite');

        $tr->add(function() use ($repo, $git) {
            $git->createWorkdir($repo);
        }, function() use ($repo, $git) {
            if (is_dir($git->getWorkDirPath($repo))) {
                rmdir($git->getWorkDirPath($repo));
            }
        }, 'create-workdir', 'Creating work directory');

        $tr->add(function() use ($repo, $git, $event) {
            $git->installPostReceiveHook($repo, $event->getServices()->getProperty('php.executable'));
        }, null, 'install-hooks', 'Installing post-receive hooks');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
            throw $exp;
        }
    }

    public function onRepositoryFork(RepositoryForkEvent $event)
    {
        $logger     = $event->getServices()->get('logger');
        $repo       = $event->getRepository();
        $fork       = $event->getFork();
        $gitDao     = $event->getServices()->get('gitDao');
        $owner      = $event->getServices()->get('usersDao')->findOne($fork->getOwner_id(), UsersDao::FIND_ID);

        $logger->addInfo(sprintf('[RepositoryForkEvent:%s] Repository created (forked from "%s") by "%s". Generating new gitolite.conf ...', $fork->getFullname(), $repo->getFullname(), $owner->getFullname()));

        $gitoliteConfig = $this->getGitoliteConfigAsString($gitDao, $event->getServices()->getProperty('forgery.user.name'));

        $file       = rtrim($this->gitolitePath, DIRECTORY_SEPARATOR)  .
            DIRECTORY_SEPARATOR . ClassicSetupService::GITOLITE_CONFIG_FILE;

        if (!is_file($file)) {
            $logger->addCritical(sprintf('[RepositoryForkEvent:%s] "%s" not found in gitolite path "%s" (?)', $repo->getFullname(), ClassicSetupService::GITOLITE_CONFIG_FILE, $this->gitolitePath));
            throw new \RuntimeException('config file not found in gitolite');
        } elseif (is_file($file) && !is_writable($file)) {
            $logger->addCritical(sprintf('[RepositoryForkEvent:%s] "%s" is not writable', $repo->getFullname(), $file));
            throw new \RuntimeException('config file not writable');
        }

        $backup = file_get_contents($file);
        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $logger, $gitoliteConfig, $repo) {
            file_put_contents($file, $gitoliteConfig, LOCK_EX);
            if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
                $logger->addCritical(sprintf('[RepositoryForkEvent:%s] Unable to write config to "%s" (write failed)', $repo->getFullname(), $file));
                throw new \RuntimeException('config file not written (write failed)');
            }
        }, function() use ($file, $backup, $logger) {
            file_put_contents($file, $backup, LOCK_EX);
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, 'write-config', 'Writing gitolite.conf');

        $tr->add(function() use ($logger) {
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, null, 'gitolite-trigger', 'Refreshing gitolite');

        $git        = $event->getServices()->get('git');

        $tr->add(function() use ($fork, $git) {
            $git->createWorkdir($fork);
        }, function() use ($fork, $git) {
            if (is_dir($git->getWorkDirPath($fork))) {
                /**
                 * @todo Bugs when directory not empty. Use proc: rm -Rf
                 */
                rmdir($git->getWorkDirPath($fork));
            }
        }, 'create-workdir', 'Creating work directory');

        $tr->add(function() use ($repo, $fork, $owner, $git) {
            $git->remote($fork, 'add', $repo->getOwner()->getUsername(), $git->getRepositoryPath($repo));
            $git->remote($repo, 'add', $owner->getUsername(), $git->getRepositoryPath($fork));
            $git->remote($fork, 'update');
        }, function() use ($repo, $fork, $owner, $git) {
            try {
                $git->remote($fork, 'rm', $repo->getOwner()->getUsername());
                $git->remote($repo, 'rm', $owner->getUsername());
            } catch(\Exception $exp) {
            }
        }, 'create-remotes', 'Creating repos remotes');

        $tr->add(function() use ($fork, $repo, $git, $event) {
            $git->pull($fork, $repo->getOwner()->getUsername());
        }, null, 'pull', 'Pulling fork');

        $tr->add(function() use ($fork, $git, $event) {
            $git->installPostReceiveHook($fork, $event->getServices()->getProperty('php.executable'));
        }, null, 'install-hooks', 'Installing post-receive hooks');

        $tr->add(function() use ($fork, $git, $event) {
            $git->push($fork);
        }, null, 'push', 'Pushing fork');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
            throw $exp;
        }
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

        $file       = rtrim($this->gitolitePath, DIRECTORY_SEPARATOR)  .
            DIRECTORY_SEPARATOR . ClassicSetupService::GITOLITE_CONFIG_FILE;

        if (!is_file($file)) {
            $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] "%s" not found in gitolite path "%s" (?)', $repo->getFullname(), ClassicSetupService::GITOLITE_CONFIG_FILE, $this->gitolitePath));
            throw new \RuntimeException('config file not found in gitolite');
        } elseif (is_file($file) && !is_writable($file)) {
            $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] "%s" is not writable', $repo->getFullname(), $file));
            throw new \RuntimeException('config file not writable');
        }

        $backup = file_get_contents($file);
        $tr = new Transaction($logger);
        $tr->add(function() use ($file, $logger, $gitoliteConfig, $repo) {
            file_put_contents($file, $gitoliteConfig, LOCK_EX);
            if (!is_file($file) || file_get_contents($file) !== $gitoliteConfig) {
                $logger->addCritical(sprintf('[RepositoryDeleteEvent:%s] Unable to write config to "%s" (write failed)', $repo->getFullname(), $file));
                throw new \RuntimeException('config file not written (write failed)');
            }
        }, function() use ($file, $backup, $logger) {
            file_put_contents($file, $backup, LOCK_EX);
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, 'write-config', 'Writing gitolite.conf');

        $tr->add(function() use ($logger) {
            $this->runGitolite(sprintf("compile; %s trigger POST_COMPILE", $this->gitoliteBin), $logger);
        }, null, 'gitolite-trigger', 'Refreshing gitolite');

        $git        = $event->getServices()->get('git');

        $tr->add(function() use ($repo, $git, $gitDao, $event) {
            $forks = $gitDao->findForks($repo);
            foreach ($forks as $fork) {
                try {
                    $git->remote($fork, 'rm', $repo->getOwner()->getUsername());
                } catch(\Exception $exp) {
                }
            }

            if ($repo->hasParent()) {
                try {
                    $git->remote($repo->getParent()->get(), 'rm', $repo->getOwner()->getUsername());
                } catch(\Exception $exp) {
                }
            }
        }, null, 'rm-remotes', 'Removing remotes');

        $tr->add(function() use ($repo, $git, $event) {
            $git->delete($repo);
        }, null, 'delete', 'Deleting repository');

        try {
            $tr->start();
        } catch(TransactionException $exp) {
            $tr->rollback();
            throw $exp;
        }
    }

    protected function runGitolite($command = "", LoggerInterface $logger)
    {
        $proc = new Process('export HOME='. $this->gitHome .' && '. trim($this->gitoliteBin .' '. $command));
        $proc->run(function ($type, $buffer) use ($logger, $command) {
            $buffer = (strpos($buffer, "\n") !== false ? explode("\n", $buffer) : array($buffer));

            if ('err' !== $type) {
                array_walk($buffer, function($line) use ($logger, $command) {
                    $logger->addDebug('[gitolite:'. $command .']: '. $line);
                });
            } else {
                array_walk($buffer, function($line) use ($logger, $command) {
                    $logger->addError('[gitolite:'. $command .']: '. $line);
                });
            }
        });

        if (!$proc->isSuccessful()) {
            $logger->addCritical('[gitolite:'. $command .'] FAIL: '. $proc->getErrorOutput());
            throw new \RuntimeException($proc->getErrorOutput());
        }
    }
}