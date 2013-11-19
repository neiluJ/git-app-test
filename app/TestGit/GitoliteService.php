<?php
namespace TestGit;

use TestGit\Events\RepositoryEditEvent;
use TestGit\Events\UserSshKeyAddEvent;
use TestGit\Events\UserSshKeyRemoveEvent;
use TestGit\Events\UserChangePasswordEvent;
use TestGit\Events\RepositoryCreateEvent;

class GitoliteService
{
    const GITOLITE_ADMIN_REPO   = 'gitolite-admin';
    const GITOLITE_KEYS_DIR     = 'keydir';
    
    public function onUserChangePassword(UserChangePasswordEvent $event)
    {
        // update .htpasswd file
        $dao    = $event->getServices()->get('usersDao');
        $users  = $dao->findAll(true);
        $file   = $event->getServices()->get('apache.htpasswd.file');
        
        if (is_file($file)) {
            if (!is_writable($file)) {
                throw new \RuntimeException('Apache htpasswd file is not writable');
            }
        }
        
        $contents = "";
        foreach ($users as $usr) {
            $passwd = $usr->getHttp_password();
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
        if (!is_file($file)) {
            throw new \RuntimeException('unable to write apache htpasswd file');
        }
    }
    
    public function onUserSshKeyAdd(UserSshKeyAddEvent $event)
    {
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";
        $repo       = $event->getServices()->get('gitDao')
                      ->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        if (!$repo instanceof Model\Git\Repository) {
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
                return;
            } 
        }

        file_put_contents($file, $key->contents, LOCK_EX);
        if (!is_file($file) || file_get_contents($file) === $key->contents) {
            throw new \RuntimeException('unable to write ssh-key to gitolite');
        }
        
        $git->add($repo, array($file));
        $git->commit($repo, $event->getUser(), 'added new ssh-key');
        $git->push($repo);
    }
    
    public function onUserSshKeyRemove(UserSshKeyRemoveEvent $event)
    {
        $userName   = $event->getUser()->getUsername();
        $key        = $event->getSshKey();
        $filename   = $userName ."@". $key->title . ".pub";
        $repo       = $event->getServices()->get('gitDao')
                      ->findOne(self::GITOLITE_ADMIN_REPO, Model\Git\GitDao::FIND_NAME);
        
        if (!$repo instanceof Model\Git\Repository) {
            throw new \RuntimeException('gitolite-admin repository not found (?)');
        }
        
        $git        = $event->getServices()->get('git');
        $workDir    = $git->getWorkDirPath($repo);
        $file       = rtrim($workDir, DIRECTORY_SEPARATOR) . 
                      DIRECTORY_SEPARATOR . self::GITOLITE_KEYS_DIR . 
                      DIRECTORY_SEPARATOR . $filename;

        if (!is_file($file)) {
           throw new \RuntimeException('ssh-key not found in gitolite');
        }

        $git->rm($repo, array($file));
        $git->commit($repo, $event->getUser(), 'removed ssh-key');
        $git->push($repo);
    }
    
    public function onRepositoryEdit(RepositoryEditEvent $event)
    {
        // update gitolite-admin/conf/gitolite.conf
    }
    
    public function onRepositoryCreate(RepositoryCreateEvent $event)
    {
        // update gitolite-admin/conf/gitolite.conf
        // create workdir
        // install hooks
    }
}