<?php
namespace Nitronet\Comments\Form;

use Fwk\Form\Elements\TextArea;
use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;

class AnonymousCommentForm extends Form
{
    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $name = new Text('name', 'name');
        $name->sanitizer(new StringSanitizer())
                ->setAttr('class', 'form-control')
                ->setAttr('placeholder', 'Your Name')
                 ->filter(new NotEmptyFilter(), 'Please enter your name.')
                 ->label("Name");

        $email = new Text('email', 'email');
        $email->sanitizer(new StringSanitizer());
        $email->filter(new NotEmptyFilter(), "You must enter an email address.");
        $email->filter(new EmailFilter(), "You must enter a valid email address.");
        $email->label("Email");

        $comment = new TextArea('comment', 'comment');
        $comment->sanitizer(new StringSanitizer());
        $comment->filter(new NotEmptyFilter(), "You must enter a comment.");
        $comment->label("Comment");

        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Login');
        
        $this->addAll(array($name, $email, $comment, $submit));
    }
}