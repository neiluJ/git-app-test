<?php
namespace Nitronet\Comments;

use Fwk\Db\Connection;
use Fwk\Db\Query;
use Fwk\Events\Dispatcher;
use InvalidArgumentException;
use Nitronet\Comments\Controllers\Thread;
use Nitronet\Comments\Events\CommentAddedEvent;
use Nitronet\Comments\Events\CommentPostedEvent;
use Nitronet\Comments\Model\Comment;

class CommentsService extends Dispatcher
{
    protected $db;
    protected $options = array();

    public function __construct(Connection $db, array $options = array())
    {
        $this->db = $db;
        $this->options = array_merge(array(
            'threadsTable'  => 'comments_threads',
            'threadEntity'  => 'Nitronet\Comments\Model\Thread',
            'commentsTable' => 'comments',
            'commentEntity' => 'Nitronet\Comments\Model\Comment',
            'autoThread'        => false,
            'autoApprove'       => true,
            'dateFormat'        => 'Y-m-d H:i:s'
        ), $options);
    }

    /**
     * @param $name
     * @return ThreadInterface
     */
    public function getThread($name)
    {
        $query = Query::factory()
            ->select()
            ->from($this->option('threadsTable', 'comments_threads'), 'th')
            ->where('th.name = ?')
            ->entity($this->option('threadEntity', 'Nitronet\Comments\Model\Thread'));

        $res    = $this->getDb()->execute($query, array($name));
        $th     = (count($res) ? $res[0] : null);

        if ($th) {
            return $th;
        }

        if ($this->option('autoThread', false) == false || $this->option('autoThread', false) == "false") {
            return null;
        }

        $className = $this->option('threadEntity', 'Nitronet\Comments\Model\Thread');
        $th = new $className;

        if (!$th instanceof ThreadInterface) {
            throw new InvalidArgumentException('Class "'. $className .'" is not an instanceof ThreadInterface');
        }

        $th->setCreatedOn(date($this->option('dateFormat', 'Y-m-d H:i:s')));
        $th->setName($name);
        $th->setComments(0);
        $th->setOpen(true);

        return $th;
    }

    public function getCommentsCount($thread)
    {
        if (!$thread instanceof ThreadInterface) {
            $thread = $this->getThread($thread);
        }

        if (!$thread) {
            return 0;
        }

        return $thread->getComments();
    }

    public function getComments($thread, $sort = Thread::SORT_ASC, $type = Thread::TYPE_NORMAL)
    {
        if ($thread instanceof ThreadInterface && $thread->getComments() <= 0) {
            return array();
        }

        $query = Query::factory()
            ->select()
            ->from($this->option('commentsTable', 'comments'), 'c')
            ->where('c.thread = ?')
            ->entity($this->option('commentEntity', 'Nitronet\Comments\Model\Comment'))
            ->andWhere('c.active = 1')
            ->orderBy('c.createdOn', strtoupper($sort));

        $params = array(($thread instanceof ThreadInterface ? $thread->getName() : $thread));

        return $this->getDb()->execute($query, $params);
    }

    public function addComment($thread, CommentFormInterface $form)
    {
        $className = $this->option('commentEntity', 'Nitronet\Comments\Model\Comment');
        $comment = new $className;
        if (!$comment instanceof Comment) {
            throw new InvalidArgumentException('Class "'. $className .'" is not an instanceof CommentInterface');
        }

        if (!$thread instanceof ThreadInterface) {
            $thread = $this->getThread($thread);
        }

        if (!$thread || !$thread->isOpen()) {
            return "Thread is closed";
        }

        $event = $this->notify(new CommentPostedEvent($form, $comment, $thread, $this));

        if ($event->isStopped()) {
            return $event->getError();
        }

        if ($form->hasErrors()) {
            return $form->getErrors();
        }

        if ($form->getParentId() != null) {
            $parent = $this->getComment($form->getParentId());

            if (null === $parent) {
                return "Invalid parent comment";
            }

            if ($parent->getThread() != $thread->getName()) {
                return "Wrong parent comment (parent not on this thread)";
            }
        }

        $comment->setCreatedOn(date($this->option('dateFormat', 'Y-m-d H:i:s')));
        $comment->setAuthorEmail($form->getAuthorEmail());
        $comment->setAuthorName($form->getAuthorName());
        $comment->setAuthorUrl($form->getAuthorUrl());
        $comment->setContents($form->getComment());
        $comment->setParentId($form->getParentId());
        $comment->setThread($thread->getName());
        $comment->setActive($this->option('autoApprove', true));

        $thread->setComments((int)$thread->getComments()+1);

        $this->getDb()->beginTransaction();

        try {
            $this->getDb()->table($this->option('threadsTable', 'comments_threads'))->save($thread);
            $this->getDb()->table($this->option('commentsTable', 'comments'))->save($comment);
            $this->getDb()->commit();
        } catch(\Exception $exp) {
            $this->getDb()->rollBack();
            return $exp->getMessage();
        }

        $this->notify(new CommentAddedEvent($thread, $comment));

        return true;
    }

    /**
     * @param $id
     *
     * @return CommentInterface
     */
    public function getComment($id)
    {
        $query = Query::factory()
            ->select()
            ->from($this->option('commentsTable', 'comments'), 'c')
            ->where('c.id = ?')
            ->entity($this->option('commentEntity', 'Nitronet\Comments\Model\Comment'));

        $res = $this->getDb()->execute($query, array($id));

        if (!count($res)) {
            return null;
        }

        return $res[0];
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function option($name, $default = false)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return CommentsService
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->db;
    }
}