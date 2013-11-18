<?php
namespace TestGit\Form;

use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\TextArea;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Elements\Submit;

class AddSshKeyForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $title = new Text('title', 'title');
        $title->sanitizer(new StringSanitizer());
        $title->filter(new NotEmptyFilter(), "You must define a title.");
        $title->label("Title")
                ->setAttr('class', 'form-control');
        
        $sshkey = new TextArea('key', 'key');
        $sshkey->sanitizer(new StringSanitizer());
        $sshkey->sanitizer(new SshKeySanitizer());
        $sshkey->filter(new NotEmptyFilter(), "You must enter a key.");
        $sshkey->label("Ssh key")
                ->setAttr('class', 'form-control');
        $sshkey->filter(new SshKeyFilter(), "The SSH-Key looks invalid.");
        
        $hidden = new \Fwk\Form\Elements\Hidden('action');
        $hidden->setDefault('ssh');
        
        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
                ->setDefault('Add key');
        
        $this->addAll(array($title, $sshkey, $hidden, $submit));
    }
}