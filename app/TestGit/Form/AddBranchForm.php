<?php
namespace TestGit\Form;

use Fwk\Form\Form; 

use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;

class AddBranchForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $name = new Text('branchname', 'branchname');
        $name->sanitizer(new StringSanitizer())
                ->setAttr('class', 'form-control')
                ->setAttr('placeholder', 'ex: fix-bug12345')
                 ->filter(new NotEmptyFilter(), 'Branch name cannot be empty.')
                 ->filter(new BranchnameFilter(), 'Invalid branch name')
                 ->label("Branch Name");
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Create');
        
        $this->addAll(array($name, $submit));
    }
}