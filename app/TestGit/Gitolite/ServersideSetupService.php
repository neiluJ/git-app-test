<?php
namespace TestGit\Gitolite;

use TestGit\Events\RepositoryCreateEvent;
use TestGit\Events\RepositoryDeleteEvent;
use TestGit\Events\RepositoryEditEvent;
use TestGit\Events\RepositoryForkEvent;
use TestGit\Events\UserSshKeyAddEvent;
use TestGit\Events\UserSshKeyRemoveEvent;

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
    const DEFAULT_GITOLITE_PATH = '.gitolite';

    protected $gitolitePath;

    public function __construct($gitolitePath = self::DEFAULT_GITOLITE_PATH)
    {
        $this->gitolitePath = $gitolitePath;
    }

    public function onUserSshKeyAdd(UserSshKeyAddEvent $event)
    {
        // Gitolite doc:
        //
        // gets all the keys and dumps them into $HOME/.gitolite/keydir (or into a subdirectory of it), and
        // runs gitolite trigger SSH_AUTHKEYS.
    }

    public function onUserSshKeyRemove(UserSshKeyRemoveEvent $event)
    {

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
}