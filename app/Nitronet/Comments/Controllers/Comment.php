<?php
namespace Nitronet\Comments\Controllers;

use Fwk\Core\Action\Controller;
use Fwk\Core\Preparable;
use Fwk\Core\Action\Result;
use Fwk\Form\Form;
use Fwk\Form\Renderer;
use Nitronet\Comments\CommentFormInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class Comment extends Controller implements Preparable
{
    const SESSION_POST_KEY = 'commentData';
    const SESSION_POST_STATUS  = 'commentOk';

    public $form;
    public $thread;
    public $back = '/';

    protected $formObj;
    protected $posted = false;
    protected $status;
    protected $renderer;

    public function prepare()
    {
        if (empty($this->thread)) {
            throw new \InvalidArgumentException('No thread defined');
        }

        if (empty($this->form)) {
            $this->form = $this->getServices()->getProperty('comments.form.default');
        }

        if ($this->getSession()->has(self::SESSION_POST_STATUS)) {
            $status = $this->getSession()->get(self::SESSION_POST_STATUS, false);
            if ($status === true) {
                $this->posted = true;
            } elseif (is_string($status) || is_array($status)) {
                $this->posted = false;
                $this->status = $status;
            }

            $this->getSession()->remove(self::SESSION_POST_STATUS);
        }
    }

    public function post()
    {
        $form = $this->getFormObj();
        if ($this->getContext()->getRequest()->getMethod() != "POST") {
            if (!$this->getContext()->hasParent()) {
                // this should never happend
                return Result::FORM_ERROR;
            }

            if ($this->getSession()->has(self::SESSION_POST_KEY)) {
                $data = $this->getSession()->get(self::SESSION_POST_KEY, array());
                $form->submit($data);
                $form->validate();
                $this->getSession()->remove(self::SESSION_POST_KEY);
            }

            return Result::FORM;
        }

        $form = $this->getFormObj();
        $form->submit($_POST);

        if (!$form->validate()) {
            $this->getSession()->set(self::SESSION_POST_KEY, $_POST);
            return Result::FORM_ERROR;
        }

        $posted = $this->getService()->addComment($this->thread, $form);
        $this->getSession()->set(self::SESSION_POST_STATUS, $posted);

        if ($posted === true) {
            $this->getSession()->remove(self::SESSION_POST_KEY);
        }

        return Result::SUCCESS;
    }

    /**
     * @return Form
     */
    public function getFormObj()
    {
        if (!isset($this->formObj)) {
            $this->formObj = new $this->form;

            if (!$this->formObj instanceof CommentFormInterface) {
                throw new \InvalidArgumentException("This form is not an instance of CommentFormInterface");
            }

            $vh = $this->getServices()->get('viewHelper');
            $this->formObj->setAction(
                $vh->url(
                    $this->getContext()->getActionName(),
                    array(
                        'thread' => $this->thread,
                        'back' => $this->getContext()->getRequest()->getUri()
                    )
                )
            );
        }

        return $this->formObj;
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->getServices()->get($this->getServices()->getProperty('comments.session.service'));
    }

    /**
     * @return boolean
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return CommentsService
     */
    protected function getService()
    {
        return $this->getServices()->get($this->getServices()->getProperty('comments.service'));
    }

    /**
     * @return mixed
     */
    public function getRenderer()
    {
        if (!isset($this->renderer)) {
            $prop = $this->getServices()->getProperty('comments.form.renderer.service');
            if (empty($prop)) {
                $this->renderer = new Renderer();
            } else {
                $this->renderer = $this->getServices()->get($prop);
            }
        }

        return $this->renderer;
    }
}