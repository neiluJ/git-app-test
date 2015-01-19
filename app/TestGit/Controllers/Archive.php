<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;

class Archive extends Repository
{
    public $format;

    public function download()
    {
        try {
            $this->loadRepository('read');
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }

        echo "yeah";

        return Result::SUCCESS;
    }
}