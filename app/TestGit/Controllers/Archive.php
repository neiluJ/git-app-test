<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Response;
use TestGit\GitService;

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

        try {
            $file = $this->getGitService()->archive($this->entity, $this->branch, $this->format);
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        $response = new Response();
        $response->setExpires(new \DateTime());

        if (null !== $lastModified = filemtime($file)) {
            $date = new \DateTime();
            $date->setTimestamp($lastModified);
            $response->setLastModified($date);
        }

        $response->setETag(md5(file_get_contents($file)));

        $response->headers->set('Content-Disposition', 'attachment; filename="'. basename($file) .'"');

        if ($this->format === GitService::ARCHIVE_FORMAT_ZIP) {
            $response->headers->set('Content-type', 'application/zip');
        } elseif ($this->format === GitService::ARCHIVE_FORMAT_TAR) {
            $response->headers->set('Content-type', 'application/x-gzip');
        }

        $response->headers->set('Pragma', 'public');
        $response->headers->set('Content-Length', filesize($file));

        if ($response->isNotModified($this->getContext()->getRequest())) {
            return $response;
        }

        $response->setContent(file_get_contents($file));

        return $response;
    }
}