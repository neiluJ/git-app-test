<?php
namespace TestGit\Controllers;

use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use Fwk\Core\Preparable;
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

class Repository implements ContextAware, ServicesAware, Preparable
{
    public $name;
    public $branch;
    public $path;
    
    protected $repository;
    protected $entity;
    
    protected $services;
    protected $context;
    
    protected $files;
    
    protected $repoAction = 'Repository';
    
    protected $cloneSshUrl;
    protected $cloneHttpUrl;
    
    protected $errorMsg;
    
    protected $createForm;
    protected $forkForm;
    
    public function prepare()
    {
        if (!empty($this->path)) {
            $this->path = rtrim($this->path, '/');
        }
    }
    
    public function show()
    {
        try {
            $this->loadRepository();
        } catch(EmptyRepositoryException $exp) {
            $this->cloneUrlAction();
            return 'empty_repository';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }
        
        if (!$this->context->getRequest()->isXmlHttpRequest()) {
            return Result::SUCCESS;
        }
        
        $refs = $this->repository->getReferences();
        if ($refs->hasBranch($this->branch)) {
            $revision = $refs->getBranch($this->branch);
        } else {
            $revision = $this->repository->getRevision($this->branch);
        }
        
        $commit = $revision->getCommit();
        $tree = $commit->getTree();
        
        if (null !== $this->path) {
            $tree = $tree->resolvePath($this->path);
        }
        
        $final = array();
        
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
                $this->getGitDao()->notify(new RepositoryCreateEvent($repo, $this->getServices()));
                $this->getGitDao()->getDb()->commit();
                $this->name = $repo->getFullname();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp->getMessage();
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
            $this->loadRepository();
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
                $this->getGitDao()->notify(new RepositoryForkEvent($this->entity, $fork, $this->getServices()));
                $this->getGitDao()->getDb()->commit();
                $this->name = $fork->getFullname();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp->getMessage();
                $this->getGitDao()->getDb()->rollBack();
                return Result::ERROR;
            }
        
            return Result::SUCCESS;
        }
        
        return Result::FORM;
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
            $sc->get('git.user.name'),
            $sc->get('git.clone.hostname.ssh.remote'),
            $this->entity->getPath()
        );
        
        if ((int)$sc->get('git.clone.http') <= 0) {
            return Result::SUCCESS;
        }
        
        $this->cloneHttpUrl = sprintf(
            'http%s://%s/%s/%s',
             ((int)$sc->get('git.clone.https') > 0 ? 's' : ''),
             $sc->get('git.clone.hostname.http.remote'),
             $sc->get('git.clone.http.prefix'),
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
    protected function loadRepository()
    {
        if (isset($this->entity)) {
            return;
        }
        
        $this->entity = $this->getGitDao()
                ->findOne($this->name, \TestGit\Model\Git\GitDao::FIND_FULLNAME);
    
        if (!$this->entity instanceof RepositoryEntity) {
            throw new \Exception('repository not found');
        }

        $this->branch = (!isset($this->branch) ? $this->entity->getDefault_branch() : $this->branch);
        
        if ($this->getGitService()->isEmpty($this->entity)) {
            throw new EmptyRepositoryException('empty repository');
        }
        
        $this->repository = $this->getGitService()->transform($this->entity);
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
            
            // define possible owners
            try {
                $user = $this->getServices()->get('security')->getUser();
                $fn = $user->getFullname();
                $owners = array($user->getId() => (!empty($fn) ? $fn : $user->getUsername()));
                
                $this->createForm->element('owner_id')->setOptions($owners);
                $this->createForm->element('owner_id')->filter(new IsInArrayFilter(array_keys($owners)));
                $this->createForm->element('owner_id')->setDefault($user->getId());
                
                /**
                 * @todo ROLE_ADMIN can create repositories to anyone
                 * @todo Organizations
                 */
            } catch(\Fwk\Security\Exceptions\AuthenticationRequired $exp) {
            }
        }
        
        return $this->createForm;
    }
    
    public function getForkForm()
    {
        if (!isset($this->forkForm)) {
            $this->forkForm = new CreateForkForm();
            $this->forkForm->setAction($this->getServices()->get('viewHelper')->url('Fork', array('name' => $this->name)));
            
            // define possible owners
            try {
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
            } catch(\Fwk\Security\Exceptions\AuthenticationRequired $exp) {
            }
        }
        
        return $this->forkForm;
    }
    
    public function isPOST()
    {
        return "POST" === $_SERVER['REQUEST_METHOD'];
    }
}