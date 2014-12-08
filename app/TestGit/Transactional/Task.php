<?php
namespace TestGit\Transactional;

class Task
{
    protected $name;
    protected $description;
    protected $action;
    protected $rollbackAction;

    public function __construct($name = null, $description = null)
    {
        $this->description  = $description;
        $this->name         = (!empty($name) ? $name : "Anonymous Task");
    }

    public function setAction($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(
                sprintf('Action for task: %s (%s) is not callable', $this->name, $this->description)
            );
        }

        $this->action = $callable;
        return $this;
    }

    public function setRollbackAction($callable = null)
    {
        if (null !== $callable && !is_callable($callable)) {
            throw new \InvalidArgumentException(
                sprintf('Rollback Action for task: %s (%s) is not callable', $this->name, $this->description)
            );
        }

        $this->rollbackAction = $callable;
        return $this;
    }

    public function run()
    {
        try {
            call_user_func($this->action);
        } catch(\Exception $exp) {
            throw new TransactionException(
                sprintf('Failed to execute task "%s" (%s): %s', $this->name, $this->description, $exp->getMessage()),
                $exp
            );
        }
    }

    public function rollback()
    {
        if (null === $this->rollbackAction) {
            return;
        }

        try {
            call_user_func($this->rollbackAction);
        } catch(\Exception $exp) {
            throw new TransactionException(
                sprintf('Failed to rollback task "%s" (%s): %s', $this->name, $this->description, $exp->getMessage()),
                $exp
            );
        }
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }
}