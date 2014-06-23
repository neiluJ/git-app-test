<?php
namespace TestGit\Form;

use Fwk\Form\Form; 

use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Password;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Elements\Checkbox;
use Fwk\Form\Elements\Group;

use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Validation\LengthFilter;

class AddOrganizationForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $username = new Text('username', 'username');
        $username->sanitizer(new StringSanitizer())
                ->setAttr('class', 'form-control')
                ->setAttr('placeholder', 'Organization username')
                 ->filter(new NotEmptyFilter(), 'Username cannot be empty.')
                 ->filter(new UsernameFilter(), 'Invalid credentials.')
                 ->label("Organization username");

        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Add');
        
        $this->addAll(array($username, $submit));
    }
}