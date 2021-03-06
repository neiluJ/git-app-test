<?php
namespace TestGit\Form;

use Fwk\Form\Form; 

use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Password;
use Fwk\Form\Elements\Submit;

use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;

class LoginForm extends Form
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
        
        $passwd = new Password('password', 'password');
        $passwd->sanitizer(new StringSanitizer())
                ->setAttr('placeholder', 'Password')
                ->setAttr('class', 'form-control')
                ->filter(new NotEmptyFilter(), 'You must enter a password.')
                ->label("Password");
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Login');
        
        $this->addAll(array($username, $passwd, $submit));
    }
}