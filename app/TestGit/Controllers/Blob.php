<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Response;
use Fwk\Core\ContextAware;
use Fwk\Core\Context;
use TestGit\Model\Git\GitDao;

class Blob extends Commits implements ContextAware
{
    protected $blob;
    
    protected $context;
    
    protected $language;
    protected $type;

    protected $blame;
    protected $blameCommits = array();

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
        'objective-c' => array('m', 'h'),
        'cs'    => array('cs'),
        'sql'   => array('sql'),
        'ini'   => array('ini', 'ini-dist'),
        'apache'    => array('htaccess'),
        'diff'  => array('patch', 'diff'),
        'bash'  => array('sh', 'bash')
    ); 
    
    public function blobAction()
    {
        try {
            $this->loadRepository('read');
            $refs = $this->repository->getReferences();
            if ($refs->hasBranch($this->branch)) {
                $revision = $refs->getBranch($this->branch);
            } else {
                $revision = $this->repository->getRevision($this->branch);
            }

            $commit = $revision->getCommit();
            $this->currentCommit = $commit;

            $tree = $commit->getTree();

            if (is_string($tree)) {
                $tree = $this->repository->getTree($tree);
            }

            if (null !== $this->path && $tree instanceof \Gitonomy\Git\Tree) {
                $tree = $tree->resolvePath($this->path);
            }

            $this->commit = $this->repository->getLog(
                $revision, ltrim($this->path,'/'), 0, 1
            )->getSingleCommit();
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }
        
        if (!$tree instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }
        
        $this->blob = $tree;
        $this->repoAction = 'RepositoryNEW';
        
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


    public function blame()
    {
        $res = $this->blobAction();

        if ($res == Result::ERROR || !$this->blob instanceof \Gitonomy\Git\Blob) {
            return Result::ERROR;
        }

        if (!$this->blob->isText()) {
            $this->errorMsg = "Cannot blame this type of file";
            return Result::ERROR;
        }

        try {
            $this->blame = $this->repository->getBlame($this->branch, $this->path);
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        $commitsToFind = array();
        foreach ($this->blame->getGroupedLines() as $info) {
            $commitsToFind[$info[0]->getHash()] = 1;
        }

        try {
            $user = $this->getServices()->get('security')->getUser();
        } catch(\Exception $exp) {
            $user = null;
        }

        $commits = $this->getGitDao()->findCommits(array_keys($commitsToFind), GitDao::FIND_COMMITS_HASHES, $user, $this->name);
        foreach ($commits as $commit) {
            $this->blameCommits[$commit->getHash()] = $commit;
        }

        unset($commitsToFind, $commits);

        return Result::SUCCESS;
    }

    public function showNew()
    {
        $this->type = $this->show();

        return ($this->type != Result::ERROR ? Result::SUCCESS : Result::ERROR);
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

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getBlame()
    {
        return $this->blame;
    }

    /**
     * @return array
     */
    public function getBlameCommits()
    {
        return $this->blameCommits;
    }
}