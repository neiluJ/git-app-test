<?php
namespace TestGit\Form;

use Fwk\Form\Elements\Hidden;
use Fwk\Form\Elements\TextArea;
use Fwk\Form\Form;
use Fwk\Form\Elements\Submit;
use Fwk\Form\Sanitization\IntegerSanitizer;
use Fwk\Form\Sanitization\StringSanitizer;
use Fwk\Form\Validation\NotEmptyFilter;
use Fwk\Form\Validation\RegexFilter;
use Nitronet\Comments\CommentFormInterface;

class CommentForm extends Form implements CommentFormInterface
{
    protected $authorName;
    protected $authorUrl;
    protected $authorEmail;

    public function __construct($action = null, $method = 'post', 
        array $options = array()
    ) {
        parent::__construct($action, $method, $options);
        
        $parent = new Hidden('parent', 'parent');
        $parent->sanitizer(new IntegerSanitizer());
        $parent->filter(new RegexFilter('/[0-9]{0,11}/'), "Invalid parent comment");
        $parent->setDefault(null);

        $comment = new TextArea('comment', 'comment');
        $comment->sanitizer(new StringSanitizer());
        $comment->filter(new NotEmptyFilter(), "You must enter a comment.");
        $comment->setAttr('placeholder', 'Enter a comment');
        $comment->setAttr('class', 'form-control');

        $submit = new Submit();
        $submit->setAttr('class', 'btn btn-default')
               ->setDefault('Post comment');
        
        $this->addAll(array($comment, $parent, $submit));
    }

    public function getParentId()
    {
        $val = $this->element('parent')->valueOrDefault();
        return (empty($val) ? null : (int)$val);
    }

    public function setAuthorName($name)
    {
        $this->authorName = $name;
    }

    public function getAuthorName()
    {
        return $this->authorName;
    }

    public function getComment()
    {
        return $this->element('comment')->valueOrDefault();
    }

    /**
     * @param mixed $authorUrl
     */
    public function setAuthorUrl($authorUrl)
    {
        $this->authorUrl = $authorUrl;
    }

    /**
     * @return mixed
     */
    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    /**
     * @param mixed $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return mixed
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }
}