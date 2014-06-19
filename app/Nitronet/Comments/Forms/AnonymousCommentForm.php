<?php
namespace Nitronet\Comments\Forms;

use Fwk\Form\Elements\Hidden;
use Fwk\Form\Elements\TextArea;
use Fwk\Form\Form;
use Fwk\Form\Elements\Text;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Sanitization\IntegerSanitizer;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Validation\EmailFilter;
use Fwk\Form\Validation\RegexFilter;
use Fwk\Form\Validation\UrlFilter;
use Nitronet\Comments\CommentFormInterface;

class AnonymousCommentForm extends Form implements CommentFormInterface
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
        $email->setAttr('placeholder', 'you@example.com')->setAttr('class', 'form-control');
        $email->label("Email");

        $url = new Text('url', 'url');
        $url->sanitizer(new StringSanitizer());
        $url->filter(new UrlFilter(), "You must enter a valid URL.");
        $url->setAttr('placeholder', 'http://www.example.com')->setAttr('class', 'form-control');
        $url->label("Website");

        $parent = new Hidden('parent', 'parent');
        $parent->sanitizer(new IntegerSanitizer());
        $parent->filter(new RegexFilter('/[0-9]{0,11}/'), "Invalid parent comment");
        $parent->setDefault(null);

        $comment = new TextArea('comment', 'comment');
        $comment->sanitizer(new StringSanitizer());
        $comment->filter(new NotEmptyFilter(), "You must enter a comment.");
        $comment->setAttr('placeholder', 'Enter a comment');
        $comment->label("Comment")->setAttr('class', 'form-control');

        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Post comment');
        
        $this->addAll(array($name, $email, $url, $comment, $parent, $submit));
    }

    public function getParentId()
    {
        $val = $this->element('parent')->valueOrDefault();
        return (empty($val) ? null : (int)$val);
    }

    public function getAuthorName()
    {
        return $this->element('name')->valueOrDefault();
    }

    public function getAuthorEmail()
    {
        return $this->element('email')->valueOrDefault();
    }

    public function getAuthorUrl()
    {
        return $this->element('url')->valueOrDefault();
    }

    public function getComment()
    {
        return $this->element('comment')->valueOrDefault();
    }


}