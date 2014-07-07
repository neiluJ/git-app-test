<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Gitonomy\Git\Exception\ProcessException;
use TestGit\EmptyRepositoryException;

class Compare extends Repository
{
    public $compare;

    protected $commits      = array();
    protected $targets      = array();
    protected $diff;
    protected $target;
    protected $base;
    protected $baseRef;
    protected $targetRef;
    protected $mergeSuccess;
    protected $mergeMsg;

    public function prepare()
    {
        parent::prepare();
    }
    
    public function compareAction()
    {
        try {
            $this->loadRepository('read');
        } catch(EmptyRepositoryException $exp) {
            return 'empty_repo';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }

        $forks  = $this->getServices()->get('gitDao')->findForks($this->entity);
        $this->targets = $this->loadRepositoriesAcls($forks);
        $post   = ($this->getContext()->getRequest()->getMethod() == "POST");

        if (empty($this->compare)) {
            if (!$post) {
                $this->target = $this->entity->getOwner()->getUsername();
                return Result::SUCCESS;
            }

            $this->compare = $this->makeCompareStringFromPost();
            if ($this->compare !== false) {
                return Result::REDIRECT;
            }
            return Result::SUCCESS;
        }

        $res = $this->parseCompareString();
        if (!$res) {
            return Result::SUCCESS;
        }

        try {
            $this->diff = $this->repository->getDiff($this->compare);
            $this->commits = $this->repository->getRevision($this->compare)->getLog(null, 0);
        } catch(ProcessException $exp) {
            $this->errorMsg = $exp;
        }

        return Result::SUCCESS;
    }

    public function mergeAction()
    {
        $res = $this->compareAction();
        if ($res != Result::SUCCESS || null !== $this->errorMsg) {
            return Result::ERROR;
        }

        $service    = $this->getGitService();
        $post       = ($this->getContext()->getRequest()->getMethod() == "POST");

        $result = $service->tryMerge($this->entity, $this->baseRef, $this->entity, $this->targetRef);

        $this->mergeMsg = $result->message;
        $this->mergeSuccess = $result->success;

        if (!$this->mergeSuccess) {
            return 'merge_error';
        }

        if ($post) {
            $res = $service->merge($this->entity, $this->baseRef, $this->entity, $this->targetRef, $this->getServices()->get('security')->getUser(), false);
            die(print_r($res, true));
        }

        return Result::SUCCESS;
    }

    public function getCommits()
    {
        return $this->commits;
    }

    public function getDiff()
    {
        return $this->diff;
    }

    protected function loadRepositoriesAcls($repositories)
    {
        $security   = $this->getServices()->get('security');
        $acl        = $security->getAclManager();
        $final      = array($this->entity);

        foreach ($repositories as $repo) {
            if (!$acl->hasResource($repo)) {
                $acl->addResource($repo, 'repository');
            }

            $acl->deny(null, $repo);

            if (!$repo->isPrivate()) {
                $acl->allow(null, $repo, 'read');
            }
        }

        try {
            $user = $security->getUser();
            foreach ($user->getAccesses() as $access) {
                $repository = null;
                foreach ($repositories as $repo) {
                    if ($repo->getId() == $access->getRepository_id()) {
                        $repository = $repo;
                        break;
                    }
                }

                if (null === $repository) {
                    continue;
                }

                if ($repository->getOwner_id() == $user->getId()) {
                    $acl->allow($user, $repository, 'owner');
                }

                if ($access->getUser_id() === $user->getId()) {
                    if ($access->getReadAccess()) {
                        $acl->allow($user, $repository, 'read');
                    }
                    if ($access->getWriteAccess()) {
                        $acl->allow($user, $repository, 'write');
                    }
                    if ($access->getSpecialAccess()) {
                        $acl->allow($user, $repository, 'special');
                    }
                    if ($access->getAdminAccess()) {
                        $acl->allow($user, $repository, 'admin');
                    }
                }
            }

            if (isset($this->profile) && $this->profile->isOrganization()) {
                foreach ($repositories as $repo) {
                    if ($this->profile->isOrgMember($user)) {
                        $acl->allow($user, $repo, 'read');
                    }
                }
            }

        } catch(\Fwk\Security\Exceptions\AuthenticationRequired $exp) {
            $user = new \Zend\Permissions\Acl\Role\GenericRole('guest');
        }

        foreach ($repositories as $repo) {
            if ($acl->isAllowed($user, $repo, 'read')) {
                $final[] = $repo;
            }
        }

        return $final;
    }

    /**
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @return array
     */
    public function getTarget()
    {
        return $this->target;
    }

    protected function makeCompareStringFromPost()
    {
        $post = ($this->getContext()->getRequest()->getMethod() == "POST");
        if (!$post || !isset($_POST['base']) || !isset($_POST['baseRef']) || !isset($_POST['target']) || !isset($_POST['targetRef'])) {
            $this->errorMsg = "Invalid POST data.";
            return false;
        }

        $base = (empty($_POST['base']) ? $this->entity->getOwner()->getUsername() : $_POST['base']);
        $target = (empty($_POST['target']) ? $this->entity->getOwner()->getUsername() : $_POST['target']);
        $baseRef = (empty($_POST['baseRef']) ? $this->entity->getDefault_branch() : $_POST['baseRef']);
        $targetRef = (empty($_POST['targetRef']) ? $this->entity->getDefault_branch() : $_POST['targetRef']);

        if ($base != $target) {
            return sprintf("%s:%s..%s:%s", $base, $baseRef, $target, $targetRef);
        } else {
            return sprintf("%s..%s", $baseRef, $targetRef);
        }
    }

    protected function parseCompareString()
    {
        if (empty($this->compare)) {
            $this->errorMsg = "Empty comparision";
            return false;
        }

        if (strpos($this->compare, "..") === false) {
            $this->errorMsg = "Invalid comparision string";
            return false;
        }

        list($base, $target) = explode('..', $this->compare);
        if (strpos($base, ':') !== false) {
            list($baseOwner, $baseRef) = explode(':', $base);
        } else {
            $baseOwner = $this->entity->getOwner()->getUsername();
            $baseRef = $base;
        }

        if (strpos($target, ':') !== false) {
            list($targetOwner, $targetRef) = explode(':', $target);
        } else {
            $targetOwner = $this->entity->getOwner()->getUsername();
            $targetRef = $target;
        }

        $this->base = $baseOwner;
        $this->baseRef = $baseRef;
        $this->target = $targetOwner;
        $this->targetRef = $targetRef;

        return true;
    }

    /**
     * @return mixed
     */
    public function getBaseRef()
    {
        return $this->baseRef;
    }

    /**
     * @return mixed
     */
    public function getTargetRef()
    {
        return $this->targetRef;
    }

    /**
     * @return mixed
     */
    public function getBase()
    {
        return $this->base;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @return mixed
     */
    public function getMergeMsg()
    {
        return $this->mergeMsg;
    }

    /**
     * @return mixed
     */
    public function getMergeSuccess()
    {
        return $this->mergeSuccess;
    }
}