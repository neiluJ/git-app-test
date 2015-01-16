<?php
namespace TestGit\Form;

use Fwk\Form\Elements\TextArea;
use Fwk\Form\Form;

use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;

class AddTagForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $name = new Text('tagname', 'tagname');
        $name->sanitizer(new StringSanitizer())
                ->setAttr('class', 'form-control')
                ->setAttr('placeholder', 'ex: v1.5.x')
                 ->filter(new NotEmptyFilter(), 'Tag name cannot be empty.')
                 ->filter(new BranchnameFilter(), 'Invalid tag name')
                 ->label("Tag Name");

        $annotation = new TextArea('annotation', 'annotation');
        $annotation->sanitizer(new StringSanitizer())
            ->setAttr('class', 'form-control')
            ->setAttr('placeholder', 'Release description')
            ->label("Annotation (optional)");

        $ref = new Text('reference', 'reference');
        $ref->sanitizer(new StringSanitizer())
            ->setAttr('class', 'form-control')
            ->setAttr('placeholder', 'commit hash, branch name ...')
            ->filter(new NotEmptyFilter(), 'Tag reference cannot be empty.')
            ->filter(new BranchnameFilter(), 'Invalid tag reference')
            ->label("Reference");

        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Create');
        
        $this->addAll(array($name, $ref, $annotation, $submit));
    }
}