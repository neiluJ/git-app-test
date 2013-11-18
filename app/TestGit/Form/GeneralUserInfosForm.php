<?php
namespace TestGit\Form;

use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Validation\EmailFilter;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Elements\Submit;

class GeneralUserInfosForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $fullname = new Text('fullname', 'fullname');
        $fullname->sanitizer(new StringSanitizer());
        $fullname->label("Full name")
                ->setAttr('class', 'form-control');
        
        $email = new Text('email', 'email');
        $email->sanitizer(new StringSanitizer());
        $email->filter(new NotEmptyFilter(), "You must enter an email address.");
        $email->filter(new EmailFilter(), "You must enter a valid email address.");
        $email->label("Email")
                ->setAttr('class', 'form-control');
        
        $hidden = new \Fwk\Form\Elements\Hidden('action');
        $hidden->setDefault('infos');
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
                ->setDefault('Save Informations');
        
        $this->addAll(array($fullname, $email, $hidden, $submit));
    }
}