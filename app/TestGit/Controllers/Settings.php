<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use TestGit\EmptyRepositoryException;
use TestGit\Form\GeneralRepoInfosForm;

class Settings extends Repository
{
    protected $generalInfosForm;
    public $updated;
    
    public function show()
    {
        try {
            $this->loadRepository();
            $this->cloneUrlAction();
        } catch(EmptyRepositoryException $exp) {
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        $form = $this->getGeneralInfosForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
           $this->entity->setDescription($form->description);
           $this->entity->setWebsite($form->website);
           
           try {
               $this->getGitDao()->save($this->entity);
           } catch(\Exception $exp) {
               $this->errorMsg = $exp->getMessage();
               return Result::ERROR;
           }
           
           return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function getGeneralInfosForm()
    {
        if (!isset($this->generalInfosForm)) {
            $this->generalInfosForm = new GeneralRepoInfosForm();
            
            if (null !== $this->entity) {
                $this->generalInfosForm->setAction($this->getServices()->get('viewHelper')->url('Settings', array('name' => $this->entity->getFullname())));
                $this->generalInfosForm->element('description')->setDefault($this->entity->getDescription());
                $this->generalInfosForm->element('website')->setDefault($this->entity->getWebsite());
            }
        }
        return $this->generalInfosForm;
    }
}