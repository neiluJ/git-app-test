<?php
namespace Nitronet\Comments\Events;

use Fwk\Events\Event;
use Nitronet\Comments\CommentFormInterface;
use Nitronet\Comments\CommentsService;

class CommentPostedEvent extends Event
{
    const NAME = 'commentPosted';

    public function __construct(CommentFormInterface $commentForm, CommentsService $service)
    {
        parent::__construct(self::NAME, array(
            'commentForm'   => $commentForm,
            'service'       => $service
        ));
    }

    /**
     * @return CommentsService
     */
    public function getService()
    {
        return $this->service;
    }


    /**
     * @return ThreadInterface
     */
    public function getCommentForm()
    {
        return $this->commentForm;
    }
}