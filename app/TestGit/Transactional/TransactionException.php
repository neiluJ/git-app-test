<?php
namespace TestGit\Transactional;

class TransactionException extends \RuntimeException
{
    public function __construct($message, \Exception $exp = null)
    {
        parent::__construct($message, null, $exp);
    }
}