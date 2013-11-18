<?php
namespace TestGit\Form;

use Fwk\Form\Form;
use Fwk\Form\Elements\Password;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Validation\LengthFilter;
use Fwk\Form\Elements\Submit;

class ChangePasswordForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $cpasswd = new Password('current_password', 'current_password');
        $cpasswd->sanitizer(new StringSanitizer());
        $cpasswd->filter(new NotEmptyFilter(), 'You must enter your password.');
        $cpasswd->label("Current Password")
                ->setAttr('class', 'form-control');
        
        $passwd = new Password('password', 'password');
        $passwd->sanitizer(new StringSanitizer());
        $passwd->filter(new NotEmptyFilter(), 'You must enter a new password.');
        $passwd->filter(new LengthFilter(6), 'Your password is too short.');
        $passwd->label("Password")
                ->setAttr('class', 'form-control');
        
        $passwd2 = new Password('password_verif', 'password_verif');
        $passwd2->sanitizer(new StringSanitizer());
        $passwd2->filter(new NotEmptyFilter(), 'You must confirm your new password.');
        $passwd2->label("Confirm")
                ->setAttr('class', 'form-control');
        
        $hidden = new \Fwk\Form\Elements\Hidden('action');
        $hidden->setDefault('passwd');
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Change password');
        
        $this->addAll(array($cpasswd, $passwd, $passwd2, $hidden, $submit));
    }
}