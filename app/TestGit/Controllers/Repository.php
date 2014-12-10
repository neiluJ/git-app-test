<?php
namespace TestGit\Controllers;

use Fwk\Core\Accessor;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use Fwk\Core\Preparable;
use Fwk\Security\Exceptions\AuthenticationRequired;
use TestGit\Model\Git\Repository as RepositoryEntity;
use Fwk\Core\ContextAware;
use Fwk\Core\Context;
use TestGit\Form\CreateRepositoryForm;
use Fwk\Form\Validation\IsInArrayFilter;
use TestGit\Events\RepositoryCreateEvent;
use TestGit\EmptyRepositoryException;
use TestGit\Form\CreateForkForm;
use TestGit\Model\Git\GitDao;
use TestGit\Events\RepositoryForkEvent;
use TestGit\Events\RepositoryDeleteEvent;

class Repository implements ContextAware, ServicesAware, Preparable
{
    public $name;
    public $branch;
    public $path;
    
    protected $repository;
    protected $entity;
    protected $commit;
    
    protected $services;
    protected $context;
    
    protected $files;
    
    protected $repoAction = 'RepositoryNEW';
    
    protected $cloneSshUrl;
    protected $cloneHttpUrl;
    protected $clonePublicUrl;
    
    protected $errorMsg;
    
    protected $createForm;
    protected $forkForm;

    protected $emptyRepo = false;
    protected $readme = null;
    
    public function prepare()
    {
        if (!empty($this->path)) {
            $this->path = rtrim($this->path, '/');
        }
    }
    
    public function show()
    {
        try {
            $this->loadRepository('read');
        } catch(EmptyRepositoryException $exp) {
            $this->cloneUrlAction();
            return 'empty_repository';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }

        $refs = $this->repository->getReferences();
        if ($refs->hasBranch($this->branch)) {
            $revision = $refs->getBranch($this->branch);
        } else {
            $revision = $this->repository->getRevision($this->branch);
        }
        
        $commit = $revision->getCommit();
        $tree = $commit->getTree();

        if (is_string($tree)) {
            $tree = $this->repository->getTree($tree);
        }

        if (null !== $this->path) {
            $tree = $tree->resolvePath($this->path);
        }
        
        $final = array();

        $this->commit = $this->repository->getLog(
            $revision, ltrim($this->path,'/'), 0, 1
        )->getSingleCommit();

        foreach ($tree->getEntries() as $fileName => $infos) {
            $dir = ($infos[0] === '040000' ? true : false);
            $log = $this->repository->getLog($revision, (!empty($this->path) ? ltrim($this->path,'/') . '/' : '') . $fileName, 0, 1);
            $commit = $log->getSingleCommit();
            $final[($infos[0] === '040000' ? 0 : 1) . $fileName] = array(
                'path'          => $fileName,
                'directory'     => $dir,
                'realpath'      => (!empty($this->path) ? $this->path . DIRECTORY_SEPARATOR : '') . $fileName,
                'special'       => false,
                'lastCommit'    => array(
                    'hash'          => $commit->getHash(),
                    'author'        => $commit->getCommitterName(),
                    'date'          => $commit->getCommitterDate()->format('d/m/y'),
                    'message'       => $commit->getShortMessage(55)
                )
            );

            if (!$dir && strpos($fileName, 'README', 0) !== false) {
                $this->readme = (!empty($this->path) ? $this->path . DIRECTORY_SEPARATOR : '') . $fileName;
            }
        }
        
        if (!empty($this->path)) {
            $prev = explode('/', $this->path);
            unset($prev[count($prev)-1]);
            
            $final['00'] = array(
                'path'          => '..',
                'directory'     => true,
                'special'       => true,
                'realpath'      => implode('/', $prev),
                'lastCommit'    => array(
                    'hash'          => null,
                    'author'        => null,
                    'date'          => null,
                    'message'       => null
                )
            );
        }
        
        ksort($final);
        $this->files = $final;
        
        return Result::SUCCESS;
    }
    
    public function create()
    {
        $form = $this->getCreateForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            $this->getGitDao()->getDb()->beginTransaction();
            try {
                $repo = $this->getGitDao()->create(
                    $this->getUsersDao()->getById($form->owner_id), 
                    $form->name, 
                    $form->description, 
                    ($form->type == 'public' ? true : false),
                    GitDao::TYPE_REPOSITORY
                );

                $this->getGitDao()->save($repo);
                $this->getGitDao()->notify(new RepositoryCreateEvent(
                    $repo,
                    $this->getServices()->get('security')->getUser(),
                    $this->getServices())
                );
                $this->getGitDao()->getDb()->commit();
                $this->name = $repo->getFullname();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp;
                $this->getGitDao()->getDb()->rollBack();
                return Result::ERROR;
            }
        
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function fork()
    {
        try {
            $this->loadRepository('read');
        } catch(EmptyRepositoryException $exp) {
            return 'empty_repo';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        $form = $this->getForkForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            $this->getGitDao()->getDb()->beginTransaction();
            try {
                $fork = $this->getGitDao()->create(
                    $this->getUsersDao()->getById($form->owner_id), 
                    $this->entity->getName(), 
                    $this->entity->getDescription(), 
                    ($form->type == 'public' ? true : false),
                    GitDao::TYPE_FORK,
                    $this->entity,
                    $this->entity->getDefault_branch()
                );

                $fork->setLast_commit_hash($this->entity->getLast_commit_hash());
                $fork->setLast_commit_author($this->entity->getLast_commit_author());
                $fork->setLast_commit_date($this->entity->getLast_commit_date());
                $fork->setLast_commit_msg($this->entity->getLast_commit_msg());
                $fork->setLanguages($this->entity->getLanguages());
                
                $this->getGitDao()->save($fork);
                $this->getGitDao()->notify(new RepositoryForkEvent(
                    $this->entity,
                    $fork,
                    $this->getServices()->get('security')->getUser(),
                    $this->getServices())
                );
                $this->getGitDao()->getDb()->commit();
                $this->name = $fork->getFullname();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp;
                $this->getGitDao()->getDb()->rollBack();
                return Result::ERROR;
            }
        
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function delete()
    {
        try {
            $this->loadRepository('owner');
        } catch(EmptyRepositoryException $exp) {
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        if ($this->isPOST()) {
            $this->getGitDao()->getDb()->beginTransaction();
            try {
                $this->getGitDao()->delete($this->entity);
                $this->getGitDao()->notify(new RepositoryDeleteEvent(
                    $this->entity,
                    $this->getServices()->get('security')->getUser(),
                    $this->getServices())
                );
                $this->getGitDao()->getDb()->commit();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp;
                $this->getGitDao()->getDb()->rollBack();
                return Result::ERROR;
            }
        
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function activity()
    {
        try {
            $this->loadRepository('read');
        } catch(EmptyRepositoryException $exp) {
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }
        
        return Result::SUCCESS;
    }
    
    public function cloneUrlAction()
    {
        try {
            $this->loadRepository();
        } catch(EmptyRepositoryException $exp) {
        } catch(\Exception $exp) {
            return Result::ERROR;
        }

        $sc = $this->getServices();
        
        $this->cloneSshUrl = sprintf(
            '%s@%s:%s', 
            $sc->getProperty('git.user.name'),
            $sc->getProperty('git.clone.hostname.ssh.remote'),
            $this->entity->getPath()
        );
        
        if ((int)$sc->getProperty('git.clone.http') <= 0) {
            return Result::SUCCESS;
        }
        
        $this->cloneHttpUrl = sprintf(
            'http%s://%s/%s/%s',
             ((int)$sc->getProperty('git.clone.https') > 0 ? 's' : ''),
             $sc->getProperty('git.clone.hostname.http.remote'),
             $sc->getProperty('git.clone.http.prefix'),
             $this->entity->getPath()   
        );
        
        $prefix = $sc->getProperty('git.clone.http.prefix.public');
        if (empty($prefix) || $this->entity->isPrivate()) {
            return Result::SUCCESS;
        }
        
        $this->clonePublicUrl = sprintf(
             'http%s://%s/%s/%s',
             ((int)$sc->getProperty('git.clone.https') > 0 ? 's' : ''),
             $sc->getProperty('git.clone.hostname.http.remote'),
             $prefix,
             $this->entity->getPath()   
        );
                
        
        return Result::SUCCESS;
    }
    
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Container $services)
    {
        $this->services = $services;
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function getFiles()
    {
        return $this->files;
    }
        /**
     * @return \TestGit\GitService
     */
    protected function getGitService()
    {
        return $this->getServices()->get('git');
    }
    
    /**
     * @return GitDao
     */
    protected function getGitDao()
    {
        return $this->getServices()->get('gitDao');
    }
    
    /**
     * @return \TestGit\Model\User\UsersDao
     */
    protected function getUsersDao()
    {
        return $this->getServices()->get('usersDao');
    }
    
    public function getRepoAction()
    {
        return $this->repoAction;
    }
    
    /**
     *
     */
    protected function loadRepository($permission = null)
    {
        if ($this->services->exists('__repo_entity')) {
            $db = $this->services->get('__repo_entity');
            $this->entity = $db['entity'];
            $this->repository = $db['repo'];
            $this->branch = $db['branch'];
        }

        if (isset($this->entity)) {
            return;
        }
        $this->entity = $this->getGitDao()
                ->findOne($this->name, GitDao::FIND_FULLNAME);

        // load repo acls
        $security = $this->getServices()->get('security');
        $acl = $security->getAclManager();
        if (!$acl->hasResource($this->entity)) {
            $acl->addResource($this->entity, 'repository');
        }
        
        if ((int)$this->getServices()->getProperty('git.clone.http') <= 0) {
            $allowHttp = false;
        } else {
            $allowHttp = true;
        }
         
        $publicPrefix = $this->getServices()->getProperty('git.clone.http.prefix.public');
        if ($this->entity->isPrivate()) {
            $acl->deny(null, $this->entity);
        } elseif ($allowHttp && !empty($publicPrefix)) {
            $acl->allow(null, $this->entity, 'read');
        }

        if ($security->hasUser()) {
            $user = $security->getUser($this->getContext()->getRequest());

            if ($this->entity->getOwner()->isOrgMember($user)) {
                $acl->allow($user, $this->entity, 'read');

                $members = $this->entity->getOwner()->getMembers();
                $access = $members[$user->getId()];
                if ((bool)$access->getReposAdminAccess()) {
                    $acl->allow($user, $this->entity, 'owner');
                    $acl->allow($user, $this->entity, 'admin');
                }
                if ((bool)$access->getReposWriteAccess()) {
                    $acl->allow($user, $this->entity, 'write');
                }
            }
        } else {
            $user = new \Zend\Permissions\Acl\Role\GenericRole('guest');
        }

        $this->entity->loadAcls($user, $acl);

        if (null !== $permission && !$acl->isAllowed($user, $this->entity, $permission)) {
            throw new \RuntimeException('You\'re not allowed to view this page');
        }
        
        $this->branch = (!isset($this->branch) ? $this->entity->getDefault_branch() : $this->branch);
        
        if ($this->getGitService()->isEmpty($this->entity)) {
            $this->emptyRepo = true;
            throw new EmptyRepositoryException('empty repository');
        }
        
        $this->repository = $this->getGitService()->transform($this->entity);

        // caching values for embeded actions (ie: README)
        $this->services->set('__repo_entity', array(
            'entity' => $this->entity,
            'repo'  => $this->repository,
            'branch' => $this->branch
        ), true);
    }
    
    /**
     *
     * @return RepositoryEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function getCloneSshUrl()
    {
        return $this->cloneSshUrl;
    }

    public function getCloneHttpUrl()
    {
        return $this->cloneHttpUrl;
    }

    public function getClonePublicUrl()
    {
        return $this->clonePublicUrl;
    }

    
    public function getContext()
    {
        return $this->context;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }
    
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
    
    public function getCreateForm()
    {
        if (!isset($this->createForm)) {
            $this->createForm = new CreateRepositoryForm();
            $this->createForm->setAction($this->getServices()->get('viewHelper')->url('Create'));

            if (!$this->getServices()->get('security')->hasUser()) {
                return $this->createForm;
            }

            // define possible owners
            $user = $this->getServices()->get('security')->getUser();
            $accesses = $user->getOrgAccesses()->fetch();
            $fn = $user->getFullname();
            $owners = array($user->getId() => (!empty($fn) ? $fn : $user->getUsername()));

            foreach ($accesses as $axs) {
                if (true === (bool)$axs->getReposAdminAccess()) {
                    $owners[$axs->getOrganization_id()] = $axs->getOrganization()->getUsername();
                }
            }

            $this->createForm->element('owner_id')->setOptions($owners);
            $this->createForm->element('owner_id')->filter(new IsInArrayFilter(array_keys($owners)));
            $this->createForm->element('owner_id')->setDefault($user->getId());
        }
        
        return $this->createForm;
    }
    
    public function getForkForm()
    {
        if (!isset($this->forkForm)) {
            $this->forkForm = new CreateForkForm();
            $this->forkForm->setAction($this->getServices()->get('viewHelper')->url('Fork', array('name' => $this->name)));

            if (!$this->getServices()->get('security')->hasUser()) {
                return $this->forkForm;
            }

            // define possible owners
            $user = $this->getServices()->get('security')->getUser();
            $fn = $user->getFullname();
            $owners = array($user->getId() => (!empty($fn) ? $fn : $user->getUsername()));
            $this->forkForm->element('owner_id')->setOptions($owners);
            $this->forkForm->element('owner_id')->filter(new IsInArrayFilter(array_keys($owners)));
            $this->forkForm->element('owner_id')->setDefault($user->getId());

            /**
             * @todo ROLE_ADMIN can create forks to anyone
             * @todo Organizations
             */
             if (isset($this->entity)) {
                $this->forkForm->element('type')->setDefault(($this->entity->isPrivate() ? 'private' : 'public'));
             }

        }
        
        return $this->forkForm;
    }
    
    public function isPOST()
    {
        return "POST" === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return boolean
     */
    public function getEmptyRepo()
    {
        return $this->emptyRepo;
    }

    /**
     * @return mixed
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @return null
     */
    public function getReadme()
    {
        return $this->readme;
    }
}