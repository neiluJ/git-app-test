<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use TestGit\Form\GeneralUserInfosForm;
use TestGit\Form\AddSshKeyForm;
use TestGit\Form\ChangePasswordForm;
use TestGit\Form\EmailAlreadyExistsFilter;
use Fwk\Form\Validation\EqualsFilter;
use TestGit\Form\PasswordVerificationFilter;
use TestGit\StringUtils;
use TestGit\Form\SshKeyExistsFilter;
use TestGit\Form\SshKeyTitleExistsFilter;
use TestGit\Events\UserSshKeyAddEvent;
use TestGit\Events\UserSshKeyRemoveEvent;
use TestGit\Events\UserChangePasswordEvent;

class UserSettings extends Profile
{
    public $updated;
    public $id;
    
    protected $generalInfosForm;
    protected $sshKeyForm;
    protected $changePasswordForm;
    protected $successMsg;
    
    public function show()
    {
        try {
            $this->loadProfile('edit');
        } catch(\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return Result::ERROR;
        }
        
        return Result::SUCCESS;
    }
    
    public function editInfos()
    {
        try {
            $this->loadProfile('edit');
        } catch(\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return Result::ERROR;
        }
        
        $form = $this->getGeneralInfosForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            $this->profile->setEmail($form->email);
            $this->profile->setFullname($form->fullname);
            
            $this->getUsersDao()->save($this->profile, false);
            
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function addSshKey()
    {
        try {
            $this->loadProfile('edit');
        } catch(\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return Result::ERROR;
        }
        
        $form = $this->getSshKeyForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            $sshkey = new \stdClass();
            $sshkey->title      = StringUtils::slugize(substr($form->title, 0, 50));
            $sshkey->contents   = $form->key;
            $sshkey->created_on = date('Y-m-d H:i:s');
            $sshkey->hash       = md5($form->key);

            $this->profile->getSshKeys()->fetch();

            $this->profile->getSshKeys()->add($sshkey);
            
            $this->getUsersDao()->getDb()->beginTransaction();
        
            try {
                $this->getUsersDao()->save($this->profile, false);
                $this->getUsersDao()->notify(new UserSshKeyAddEvent($this->profile, $sshkey, $this->getServices()));
                $this->getUsersDao()->getDb()->commit();
            } catch(\RuntimeException $exp) {
                $this->errorMsg = $exp->getMessage();
                $this->getUsersDao()->getDb()->rollBack();
                return Result::ERROR;
            }
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    
    public function revokeSshKey()
    {
        try {
            $this->loadProfile('edit');
        } catch(\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return Result::ERROR;
        }
        
        if (empty($this->id)) {
            return Result::ERROR;
        }
        
        $find = $this->getUsersDao()->findSshKeyById((int)$this->id);
        if (count($find) !== 1) {
            return Result::ERROR;
        } 
        
        foreach ($this->profile->getSshKeys() as $key) {
            if ($key->id == $find[0]->id) {
                $this->profile->getSshKeys()->remove($key);
                break;
            }
        }
        
        $this->getUsersDao()->getDb()->beginTransaction();
        
        try {
            $this->getUsersDao()->save($this->profile, false);
            $this->getUsersDao()->notify(new UserSshKeyRemoveEvent($this->profile, $find[0], $this->getServices()));
            $this->getUsersDao()->getDb()->commit();
        } catch(\RuntimeException $exp) {
            $this->errorMsg = $exp->getMessage();
            $this->getUsersDao()->getDb()->rollBack();
            return Result::ERROR;
        }
        
        return Result::SUCCESS;
    }
    
    public function chpasswd()
    {
        try {
            $this->loadProfile('edit');
        } catch(\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return Result::ERROR;
        }
        
        $form = $this->getChangePasswordForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            $form->element('password_verif')
            ->filter(
                 new EqualsFilter($form->password), 
                 "Password/Confirmation mismatch"
             );
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            $this->getUsersDao()->getDb()->beginTransaction();
            
            try {
                $this->getUsersDao()->updatePassword($this->profile, $form->password, $this->getServices()->get('users'));
                $this->getUsersDao()->save($this->profile);
                $this->getUsersDao()->notify(new UserChangePasswordEvent($this->profile, $this->getServices()));
                $this->getUsersDao()->getDb()->commit();
            } catch(\RuntimeException $exp) {
                $this->errorMsg = $exp->getMessage() . " (password NOT changed)";
                $this->getUsersDao()->getDb()->rollBack();
                return Result::ERROR;
            }
            
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function getGeneralInfosForm()
    {
        if (!isset($this->generalInfosForm)) {
            $this->generalInfosForm = new GeneralUserInfosForm();
            
            if (null !== $this->profile) {
                $this->generalInfosForm->element('fullname')->setDefault($this->profile->getFullname());
                $this->generalInfosForm->element('email')->setDefault($this->profile->getEmail());
                $this->generalInfosForm->element('email')->filter(
                    new EmailAlreadyExistsFilter($this->getUsersDao(), $this->profile),
                    "This email is already used. Please choose a different one"
                );
            }
            
            $this->generalInfosForm->setAction($this->getServices()->get('viewHelper')->url('EditUserInfos', array('username' => $this->username)));
        }
        
        return $this->generalInfosForm;
    }

    public function getSshKeyForm()
    {
        if (!isset($this->sshKeyForm)) {
            $this->sshKeyForm = new AddSshKeyForm();
            $this->sshKeyForm->element('title')->filter(new SshKeyTitleExistsFilter($this->getUsersDao(), $this->profile), "This title is already used for another key of yours");
            $this->sshKeyForm->element('key')->filter(new SshKeyExistsFilter($this->getUsersDao()), "Looks like you're trying to add an existing ssh-key");
            $this->sshKeyForm->setAction($this->getServices()->get('viewHelper')->url('AddSshKey', array('username' => $this->username)));
        }
        return $this->sshKeyForm;
    }

    public function getChangePasswordForm()
    {
        if (!isset($this->changePasswordForm)) {
            $this->changePasswordForm = new ChangePasswordForm();
            if (null !== $this->profile) {
                $this->changePasswordForm->element('current_password')->filter(new PasswordVerificationFilter($this->profile), "Invalid password.");
            }
            
            $this->changePasswordForm->setAction($this->getServices()->get('viewHelper')->url('ChangePassword', array('username' => $this->username)));
        }
        
        return $this->changePasswordForm;
    }
    
    public function isPOST()
    {
        return "POST" === $_SERVER['REQUEST_METHOD'];
    }
    
    public function getSuccessMsg()
    {
        return $this->successMsg;
    }
    
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
}