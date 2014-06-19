<?php
namespace Nitronet\Comments\Events;

use Fwk\Events\Event;
use Nitronet\Comments\ThreadInterface;
use Nitronet\Comments\CommentInterface;

class CommentAddedEvent extends Event
{
    const NAME = 'commentAdded';

    public function __construct(ThreadInterface $thread, CommentInterface $comment)
    {
        parent::__construct(self::NAME, array(
            'thread'    => $thread,
            'comment'   => $comment
        ));
    }

    /**
     * @return CommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }


    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }
}