<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Response;
use Fwk\Core\ContextAware;
use Fwk\Core\Context;

class Blob extends Commits implements ContextAware
{
    protected $blob;
    
    protected $context;
    
    protected $language;
    
    protected static $languages = array(
        'php'   => array('php', 'phtml', 'php3', 'php4', 'php5'),
        'xml'   => array('html', 'xml', 'xhtml'),
        'c'     => array('c', 'h'),
        'asp'   => array('asp', 'axd'),
        'css'   => array('css'),
        'cfm'   => array('cfm'),
        'erlang' => array('yaws'),
        'python'  => array('py'),
        'haml'  => array('haml'),
        'perl'  => array('pl'),
        'scala' => array('scala'),
        'java'  => array('java', 'jsp', 'jsf'),
        'go'    => array('go'),
        'lasso' => array('lasso'),
        'scss'  => array('scss'),
        'handlebars'    => array('hb'),
        'json'  => array('json'),
        'javascript'    => array('javascript', 'js'),
        'coffeescript'  => array('coffee'),
        'actionscript'  => array('as'),
        'vbscript' => array('vb'),
        'lua'   => array('lua'),
        'cpp'   => array('cpp'),
        'objectivec' => array('m', 'h'),
        'cs'    => array('cs'),
        'sql'   => array('sql'),
        'ini'   => array('ini'),
        'apache'    => array('htaccess'),
        'diff'  => array('patch', 'diff'),
        'bash'  => array('sh', 'bash')
    ); 
    
    public function blobAction()
    {
        try {
            $this->loadRepository();
        } catch(\Exception $exp) {
            return Result::ERROR;
        }
        
        $refs = $this->repository->getReferences();
        if ($refs->hasBranch($this->branch)) {
            $revision = $refs->getBranch($this->branch);
        } else {
            $revision = $this->repository->getRevision($this->branch);
        }
        
        $commit = $revision->getCommit();
        $this->currentCommit = $commit;
        
        $tree = $commit->getTree();
        
        if (null !== $this->path) {
            $tree = $tree->resolvePath($this->path);
        }
        
        if (!$tree instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }
        
        $this->blob = $tree;
        $this->repoAction = 'Blob';
        
        return Result::SUCCESS;
    }
    
    public function show()
    {
        $res = $this->blobAction();
        
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
            $this->language = $this->findLanguageByExtension($this->path);
            return "display_text";
        }
        
        return Result::ERROR;
    }
    
    public function raw()
    {
        $res = $this->blobAction();
        
        if ($res == Result::ERROR || !$this->blob instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }
        
        $response   = new Response();
        $response->setExpires(new \DateTime());
        
        $finfo = new \finfo(FILEINFO_MIME);
        $this->mimeType = $finfo->buffer($this->blob->getContent());
        $lastModified = $this->getCurrentCommit();
        
        if (false !== $lastModified) {
            $response->setLastModified($lastModified->getAuthorDate());
        }
        
        $response->setETag($lastModified->getHash());
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
    
    protected function isMarkdown($blob)
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
    
    public function getLanguage()
    {
        return $this->language;
    }
    
    protected function findLanguageByExtension($fullpath)
    {
        $ext = pathinfo($fullpath, PATHINFO_EXTENSION);
        foreach (self::$languages as $lang => $exts) {
            if (in_array($ext, $exts, true)) {
                return $lang;
            }
        }
        
        return false;
    }
}