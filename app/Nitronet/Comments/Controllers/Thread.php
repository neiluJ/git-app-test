<?php
namespace Nitronet\Comments\Controllers;

use Fwk\Core\Action\Controller;
use Nitronet\Comments\CommentsService;
use Fwk\Core\Action\Result;
use Nitronet\Comments\ThreadInterface;
use Fwk\Core\Preparable;

class Thread extends Controller implements Preparable
{
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    const TYPE_NORMAL = 'normal';
    const TYPE_THREADED = 'threaded';

    public $id;
    public $sort = self::SORT_ASC;
    public $type = self::TYPE_NORMAL;

    protected $thread;
    protected $error;
    protected $comments = array();
    protected $md;

    public function prepare()
    {
        $this->md = new \stdClass();
        $this->md->five = function($str) { return md5($str); };
    }

    public function show()
    {
        if (empty($this->id)) {
            $this->error = "Empty thread id";
            return Result::ERROR;
        }

        $service = $this->getService();
        $this->thread = $service->getThread($this->id);
        if (null === $this->thread) {
            $this->error = "Thread does not exists";
            return Result::ERROR;
        }

        if (!in_array($this->type, array(self::TYPE_NORMAL, self::TYPE_THREADED))) {
            $this->error = "Invalid type (normal or threaded)";
            return Result::ERROR;
        }
        if (!in_array($this->sort, array(self::SORT_ASC, self::SORT_DESC))) {
            $this->error = "Invalid sort direction (asc or desc)";
            return Result::ERROR;
        }

        $this->comments = $service->getComments($this->thread, $this->sort, $this->type);

        return Result::SUCCESS;
    }

    public function countComments()
    {
        if (empty($this->id)) {
            $this->error = "Empty thread id";
            return Result::ERROR;
        }

        return $this->getService()->getCommentsCount($this->id);
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
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getMd()
    {
        return $this->md;
    }
}