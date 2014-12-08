<?php
namespace TestGit\Transactional;

use Psr\Log\LoggerInterface;

class Transaction
{
    protected $queue;
    protected $logger;
    protected $stopped = false;
    protected $currIdx = -1;
    protected $id = null;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->queue = new \SplDoublyLinkedList();
        $this->queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO);
        $this->logger = $logger;
        $this->id = uniqid();
    }

    public function add($action, $rollbackAction = null, $taskName = null, $description = null)
    {
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
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug(sprintf('[tr:%s] starting php transaction...', $this->id));
        }

        $this->stopped = false;
        $this->queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO);
        $this->queue->rewind();

        foreach ($this->queue as $this->currIdx => $task) {
            if ($this->stopped) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->debug(sprintf('[tr:%s] transaction stopped.', $this->id));
                }

                break;
            }

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->debug(sprintf('[tr:%s] executing task #%u: %s (%s)', $this->id, $this->currIdx, $task->getName(), $task->getDescription()));
            }

            try {
                $task->run();
            } catch(TransactionException $exp) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->debug(sprintf('[tr:%s] task "%s" failed: %s', $this->id, $task->getName(), $exp->getMessage()));
                }
                throw $exp;
            }
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug(sprintf('[tr:%s] transaction completed successfully', $this->id));
        }
    }

    public function stop()
    {
        $this->stopped = true;
    }

    public function rollback()
    {
        if ($this->currIdx === -1) {
            throw new \Exception('Transaction not started');
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug(sprintf('[tr:%s] rollbacking php transaction...', $this->id));
        }

        $this->stopped = false;
        $this->queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
        $this->queue->rewind();

        foreach ($this->queue as $idx => $task) {
            if ($this->currIdx < $idx) {
                continue;
            }

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->debug(sprintf('[tr:%s] rollbacking task #%u: %s (%s)', $this->id, $idx, $task->getName(), $task->getDescription()));
            }

            try {
                $task->rollback();
            } catch(TransactionException $exp) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->debug(sprintf('[tr:%s] task "%s" failed to rollback: %s', $this->id, $task->getName(), $exp->getMessage()));
                }
                throw $exp;
            }
        }

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug(sprintf('[tr:%s] transaction rollbacked successfully', $this->id));
        }
    }
}