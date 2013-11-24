<?php
namespace TestGit\Form;

use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Validation\UrlFilter;
use Fwk\Form\Sanitization\HttpUrlSanitizer;

class GeneralRepoInfosForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $desc = new Text('description', 'description');
        $desc->sanitizer(new StringSanitizer());
        $desc->label("Description")
                ->setAttr('class', 'form-control');
        
        $www = new Text('website', 'website');
        $www->sanitizer(new StringSanitizer())
            ->sanitizer(new HttpUrlSanitizer())
            ->filter(new UrlFilter(), "This URL looks incorrect")
            ->label("Website")
            ->setAttr('class', 'form-control');
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
                ->setDefault('Save Informations');
        
        $this->addAll(array($desc, $www, $submit));
    }
}