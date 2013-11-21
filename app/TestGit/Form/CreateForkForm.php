<?php
namespace TestGit\Form;

use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Select;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Sanitization\IntegerSanitizer;
use Fwk\Form\Validation\IsInArrayFilter;

class CreateForkForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $owner_id = new Select('owner_id');
        $owner_id->sanitizer(new IntegerSanitizer());
        $owner_id->filter(new NotEmptyFilter(), "You must choose an owner.");
        $owner_id->label("Owner")
                ->setAttr('class', 'form-control');
        
        $type = new Select('type');
        $type->label('Type');
        $type->sanitizer(new StringSanitizer())
            ->filter(new NotEmptyFilter(), "You must choose a type.")
            ->filter(
                new IsInArrayFilter(array('public', 'private')), 
                "Unknown type."
            )
            ->setOptions(array(
                'public'    => 'Public',
                'private'   => 'Private'
            ))
            ->setDefault('public')
            ->setAttr('class', 'form-control');
        
        $submit = new \Fwk\Form\Elements\Submit();
        $submit->setDefault('Fork Repository')
                ->setAttr('class', 'btn btn-primary');
        
        $this->addAll(array($owner_id, $type, $submit));
    }
}