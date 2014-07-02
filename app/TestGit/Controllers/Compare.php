<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;

class Compare extends Repository
{
    public $compare;

    protected $commits      = array();
    protected $targets      = array();
    protected $diff;
    protected $currentTarget = array();

    public function prepare()
    {
        parent::prepare();
    }
    
    public function compareAction()
    {
        try {
            $this->loadRepository('read');
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        $forks = $this->getServices()->get('gitDao')->findForks($this->entity);
        $this->targets = $this->loadRepositoriesAcls($forks);

        if (empty($this->compare)) {
            $this->currentTarget = $this->entity->getOwner()->getUsername();
            return Result::SUCCESS;
        }

        $this->diff = $this->repository->getDiff($this->compare);

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
    public function getCurrentTarget()
    {
        return $this->currentTarget;
    }
}