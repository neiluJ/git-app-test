<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use Fwk\Core\Context;
use Fwk\Core\ContextAware;
use TestGit\Form\AddUserForm;
use Fwk\Form\Validation\EqualsFilter;
use TestGit\Form\UsernameAlreadyExistsFilter;
use TestGit\Form\EmailAlreadyExistsFilter;
use TestGit\Events\RepositoryEditEvent;
use TestGit\EmptyRepositoryException;

class Users extends Repository implements ContextAware
{
    protected $users = array();
    protected $jsonUsers = array();
    
    protected $accesses = array();
    
    protected $context;
    protected $errorMsg;
    protected $addUserForm;
    
    public $userId;
    
    public function show()
    {
        $this->users = $this->getUsersDao()->findAll(false);
        $this->buildJsonUsers();
        
        return Result::SUCCESS;
    }
    
    public function repositoryUsers()
    {
        try {
            $this->loadRepository();
        } catch(EmptyRepositoryException $exp) {
            // we don't care if the git-repository is not ready yet
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $repoId = $this->getEntity()->getId(); 
        $request = $this->getContext()->getRequest();
        $changes = 0;
        
        $this->accesses = $this->getGitDao()->getRepositoryAccesses($repoId);
        if ($request->getMethod() == "POST") {
        
            $post = $request->request;
            foreach ($post->get('access', array()) as $userId => $list) {
                $read       = (bool)(isset($list['read']) ? $list['read'] : false);
                $write      = (bool)(isset($list['write']) ? $list['write'] : false);
                $special    = (bool)(isset($list['special']) ? $list['special'] : false);
                $admin      = (bool)(isset($list['admin']) ? $list['admin'] : false);

                if ($read === false 
                    && $write === false 
                    && $special === false 
                    && $admin === false
                ) {
                    foreach ($this->accesses as $idx => $access) {
                        if ($access->getUser_id() == $userId) {
                            unset($this->accesses[$idx]);
                            break;
                        }
                    }
                    
                    $this->getGitDao()
                        ->removeRepositoryAccess($repoId, $userId);
                    $changes++;
                    continue;
                }
                
                foreach ($this->accesses as $idx => $access) {
                    if ($access->getUser_id() == $userId) {
                        $old = sprintf('%s%s%s%s', 
                            ($access->getReadAccess() ? '+' : '-'),
                            ($access->getWriteAccess() ? '+' : '-'),
                            ($access->getSpecialAccess() ? '+' : '-'),
                            ($access->getAdminAccess() ? '+' : '-')
                        );
                        $new = sprintf('%s%s%s%s', 
                            ($read ? '+' : '-'),
                            ($write ? '+' : '-'),
                            ($special ? '+' : '-'),
                            ($admin ? '+' : '-')
                        );
                        
                        // no changes
                        if ($old == $new) {
                            continue;
                        }
                        
                        $access->setReadAccess($read);
                        $access->setWriteAccess($write);
                        $access->setSpecialAccess($special);
                        $access->setAdminAccess($admin);
                        
                        $this->getGitDao()->saveAccess($access);
                        $changes++;
                    }
                }
            }
        }
        
        if ($changes > 0) {
            $this->getGitDao()->notify(new RepositoryEditEvent($this->entity, $this->getServices()));
        }
        
        $this->users    = $this->getUsersDao()->findNonAuthorized($repoId, true);
        
        return Result::SUCCESS;
    }
    
    public function addUser()
    {
        $form = $this->getAddUserForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            $form->element('confirm')->filter(new EqualsFilter($form->password), 'Password Confirmation mismatch');
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            $dao = $this->getUsersDao();
            $u = $dao->create($form->username, $form->password, $form->email, $this->getServices()->get('users'));
            $dao->save($u, true, $this->getServices());
            
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function addAccess()
    {
        $request = $this->getContext()->getRequest();
        if ($request->getMethod() != "POST") {
            return Result::SUCCESS;
        }
        
        $post = $request->request;
        
        try {
            $this->loadRepository();
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $repoId = $this->getEntity()->getId();
        $userId = $post->getInt('userid', 0);
        $read = (bool)$post->get('read', false);
        $write = (bool)$post->get('write', false);
        $special = (bool)$post->get('special', false);
        $admin = (bool)$post->get('admin', false);
        
        if (0 === $userId) {
            $error = "Invalid user specified.";
        } elseif ($read === false 
            && $write === false 
            && $special === false 
            && $admin === false
        ) {
            // nothing to do
            return Result::SUCCESS;
        }
        
        if (!isset($error)) {
            $authorized = $this->getGitDao()->getRepositoryAccesses($repoId, false);
            foreach ($authorized as $access) {
                if ($access->getUser_id() == $userId) {
                    $error = "User already have access to this repository";
                    break;
                }
            }
        }
        
        if (isset($error)) {
            /**
             * @todo Session FlagBag
             */
            $this->errorMsg = $error;
            return Result::ERROR;
        }
        
        try {
            $this->getGitDao()
                ->addRepositoryAccess($repoId, $userId, $read, $write, $special, $admin);
            
            $this->getGitDao()->notify(new RepositoryEditEvent($this->entity, $this->getServices()));
            
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        return Result::SUCCESS;
    }
    
    public function removeAccess()
    {
        try {
            $this->loadRepository();
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $repoId = $this->getEntity()->getId();
        $userId = (int)$this->userId;
        
        if (0 === $userId) {
            $error = "Invalid user specified.";
        } 
        
        try {
            $this->getGitDao()
                ->removeRepositoryAccess($repoId, $userId);
            $this->getGitDao()->notify(new RepositoryEditEvent($this->entity, $this->getServices()));
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        return Result::SUCCESS;
    }
    
    protected function buildJsonUsers()
    {
        $final = array();
        foreach ($this->users as $user) {
            $final[$user->getId()] = array(
                'id'        => $user->getId(),
                'username'  => $user->getUsername(),
                'emails'    => array($user->getEmail()),
                'fullname'  => $user->getFullname(),
                'added_date'     => $user->getDate_registration(),
                'active'    => $user->getActive()
            );
        }
        $this->jsonUsers = $final;
    }
    
    public function getUsers()
    {
        return $this->users;
    }
    
    public function getJsonUsers()
    {
        return $this->jsonUsers;
    }
    
    /**
     * 
     * @return \TestGit\Model\User\UsersDao
     */
    protected function getUsersDao()
    {
        return $this->getServices()->get('usersDao');
    }
    
    /**
     * 
     * @return \TestGit\Model\Git\GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    public function getAccesses()
    {
        return $this->accesses;
    }
    
    public function getContext()
    {
        return $this->context;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }
    
    public function getErrorMsg() {
        return $this->errorMsg;
    }

    public function getAddUserForm() 
    {
        if (!isset($this->addUserForm)) {
            $this->addUserForm = new AddUserForm();
            $this->addUserForm->setAction($this->getServices()->get('viewHelper')->url('AddUser'));
            $this->addUserForm->element('username')->filter(
                new UsernameAlreadyExistsFilter($this->getUsersDao()),
                "This username is already used. Please choose a different one"
            );
            $this->addUserForm->element('email')->filter(
                new EmailAlreadyExistsFilter($this->getUsersDao()),
                "This email is already used. Please choose a different one"
            );
        }
        return $this->addUserForm;
    }
    
    public function isPOST()
    {
        return "POST" === $_SERVER['REQUEST_METHOD'];
    }
}