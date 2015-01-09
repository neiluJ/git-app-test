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
            throw new \RuntimeException('transaction failed');
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
            throw new \RuntimeException('transaction failed');
        }
    }

    public function onRepositoryEdit(RepositoryEditEvent $event)
    {

    }

    public function onRepositoryCreate(RepositoryCreateEvent $event)
    {
        //
    }

    public function onRepositoryFork(RepositoryForkEvent $event)
    {

    }

    public function onRepositoryDelete(RepositoryDeleteEvent $event)
    {
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