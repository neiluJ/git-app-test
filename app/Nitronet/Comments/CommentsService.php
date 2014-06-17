<?php
namespace Nitronet\Comments;

use Fwk\Db\Connection;
use Fwk\Db\Query;
use Fwk\Events\Dispatcher;
use InvalidArgumentException;
use Nitronet\Comments\Controllers\Thread;

class CommentsService extends Dispatcher
{
    protected $db;
    protected $options = array();

    public function __construct(Connection $db, array $options = array())
    {
        $this->db = $db;
        $this->options = array_merge($options, array(
            'threadsTable'  => 'comments_threads',
            'threadEntity'  => 'Nitronet\Comments\Model\Thread',
            'commentsTable' => 'comments',
            'commentEntity' => 'Nitronet\Comments\Model\Comment',
            'commentPostAction' => 'CommentPost',
            'autoThread'        => false
        ));
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

        if ($this->option('autoThread', false)) {
            return null;
        }

        $className = $this->option('threadEntity', 'Nitronet\Comments\Model\Thread');
        $th = new $className;

        if (!$th instanceof ThreadInterface) {
            throw new InvalidArgumentException('Class "'. $className .'" is not an instanceof ThreadInterface');
        }

        $th->setCreatedOn(date('YYYY-MM-DD H:i:s'));
        $th->setName($name);
        $th->setComments(0);
        $th->setOpen(true);

        return $th;
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