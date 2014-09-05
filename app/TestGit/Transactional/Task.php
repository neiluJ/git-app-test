<?php
namespace TestGit\Transactional;

class Task
{
    protected $name;
    protected $description;

    function __construct($name = null, $description = null)
    {
        $this->description  = $description;
        $this->name         = (!empty($name) ? $name : "Anonymous Task");
    }

    public function setAction($callable)
    {

        return $this;
    }

    public function setRollbackAction($callable = null)
    {

        return $this;
    }

    public function execute()
    {

    }
}