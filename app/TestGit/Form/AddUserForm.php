<?php
namespace TestGit\Form;

use Fwk\Form\Form; 

use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Password;
use Fwk\Form\Elements\Submit;

use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;

class AddUserForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $username = new Text('username', 'username');
        $username->sanitizer(new StringSanitizer())
                ->setAttr('class', 'form-control')
                ->setAttr('placeholder', 'Username')
                 ->filter(new NotEmptyFilter(), 'Username cannot be empty.')
                 ->filter(new UsernameFilter(), 'Invalid credentials.')
                 ->label("Username");
        
        $email = new Text('email', 'email');
        $email->sanitizer(new StringSanitizer())
                ->setAttr('class', 'form-control')
                ->setAttr('placeholder', 'me@example.com')
                 ->filter(new NotEmptyFilter(), 'Email cannot be empty.')
                 ->filter(new \Fwk\Form\Validation\EmailFilter(), 'Invalid email address.')
                 ->label("Primary email");
        
        $passwd = new Password('password', 'password');
        $passwd->sanitizer(new StringSanitizer())
                ->setAttr('placeholder', 'Password')
                ->setAttr('class', 'form-control')
                ->filter(new NotEmptyFilter(), 'You must enter a password.')
                ->label("Password");
        
        $passwd2 = new Password('confirm', 'confirm');
        $passwd2->sanitizer(new StringSanitizer())
                ->setAttr('placeholder', 'Password confirmation')
                ->setAttr('class', 'form-control')
                ->filter(new NotEmptyFilter(), 'You must confirm your password.');
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Add');
        
        $this->addAll(array($username, $email, $passwd, $passwd2, $submit));
    }
}