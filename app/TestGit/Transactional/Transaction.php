<?php
namespace TestGit\Transactional;

use Psr\Log\LoggerInterface;

class Transaction
{
    const STATUS_INIT           = 0;

    const STATUS_RUN            = 1;
    const STATUS_RUN_SUCCESS    = 2;
    const STATUS_RUN_ERROR      = -1;

    const STATUS_ROLLBACK           = 4;
    const STATUS_ROLLBACK_SUCCESS   = 5;
    const STATUS_ROLLBACK_ERROR     = -2;

    protected $queue;
    protected $status = self::STATUS_INIT;
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->queue = new \SplDoublyLinkedList();
        $this->queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO);

        $this->logger = $logger;
    }

    public function add($action, $rollbackAction = null, $taskName = null,
        $description = null
    ) {
        $task = new Task($taskName, $description);
        $task->setAction($action)
             ->setRollbackAction($rollbackAction);

        $this->queue->push($task);

        return $this;
    }

    public function addTask(Task $task)
    {
        $this->queue->push($task);

        return $this;
    }

    public function start()
    {

    }

    public function stop()
    {

    }

    public function rollback()
    {

    }
}