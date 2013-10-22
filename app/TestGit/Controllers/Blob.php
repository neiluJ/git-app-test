<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Response;
use Fwk\Core\ContextAware;
use Fwk\Core\Context;

class Blob extends Repository implements ContextAware
{
    protected $blob;
    
    protected $context;
    
    public function show()
    {
        $res = $this->blob();
        
        if ($res == Result::ERROR || !$this->blob instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }
        
        if ($this->isImage($this->blob)) {
            return "display_image";
        } elseif ($this->blob->isBinary()) {
            return "display_binary";
        } elseif ($this->isMarkdown($this->blob)) {
            return "display_markdown";
        } elseif ($this->blob->isText()) {
            return "display_text";
        }
        
        return Result::ERROR;
    }
    
    public function raw()
    {
        $res = $this->blob();
        
        if ($res == Result::ERROR || !$this->blob instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }
        
        $response   = new Response();
        $response->setExpires(new \DateTime());
        
        $finfo = new \finfo(FILEINFO_MIME);
        $this->mimeType = $finfo->buffer($this->blob->getContent());
        $lastModified = $this->getCurrentCommit();
        
        if (false !== $lastModified) {
            $response->setLastModified($lastModified['date_obj']);
        }
        
        $response->setETag($lastModified['hash']);
        if ($response->isNotModified($this->context->getRequest())) {
            return $response;
        }
        
        // $response->headers->set('Content-Length', $this->blob->getSize());
        
        if ($this->blob->isText()) {
            $response->headers->set('Content-Type', 'text/plain');
        } elseif ($this->isImage($this->blob)) {
            $response->headers->set('Content-Type', $this->blob->getMimeType());
            $response->headers->set('Content-Transfer-Encoding', 'binary');
        } else {
            $tmp = explode('/', $this->path);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', array_pop($tmp)));
        }
        
        if ($response->isNotModified($this->context->getRequest())) {
            return $response;
        }
        
        $response->setContent($this->blob->getContent());
        
        return $response;
    }
    
    public function getBlob()
    {
        return $this->blob;
    }
    
    protected function isImage(\Gitonomy\Git\Blob $blob)
    {
        if ($blob->isText()) {
            return false;
        }
        
        $mime = $blob->getMimeType();
        if (strpos($mime, ' ') !== false) {
            list($mime,) = explode(' ', $mime);
        }
        
        $mime = rtrim($mime, ';');
        if (empty($mime)) {
            return false;
        }
        
        $imagesTypes = array(
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/gif',
            'image/svg',
            'image/svg+xml'
        );
        
        return in_array($mime, $imagesTypes);
    }
    
    public function isMarkdown($blob)
    {
        if (!$blob->isText()) {
            return false;
        }
        
        $ext = strtolower(pathinfo(basename($this->path), PATHINFO_EXTENSION));
        
        return ($ext === 'md' || $ext === 'markdown');
    }
    
    public function setContext(Context $context)
    {
        $this->context = $context;
    }
    
    /**
     * 
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }
}