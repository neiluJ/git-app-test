<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Fwk\Form\Validation\EqualsFilter;
use TestGit\EmptyRepositoryException;
use TestGit\Events\RepositoryBranchDeleteEvent;
use TestGit\Events\RepositoryTagDeleteEvent;
use TestGit\Form\AddBranchForm;
use TestGit\Form\AddTagForm;
use TestGit\Model\Git\Push;
use TestGit\Model\Git\Reference;

class Branches extends Repository
{
    protected $branches = array();
    protected $tags     = array();
    protected $addBranchForm;
    protected $addTagForm;
    protected $createdBranch = null;
    protected $canDeleteBranch = false;
    protected $refType = Reference::TYPE_BRANCH;

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
        
        $branches = $tags = array();
        foreach($refs->getAll() as $ref) {
            if ($ref instanceof \Gitonomy\Git\Reference\Branch) {
                $branches[$ref->getCommit()->getAuthorDate()->format('U') . $ref->getName()] = $ref;
            } else {
                $tags[$ref->getCommit()->getAuthorDate()->format('U') . $ref->getName()] = $ref;
            }
        }
        
        krsort($branches);
        krsort($tags);
        
        $this->branches = $branches;
        $this->tags     = $tags;
        $this->repoAction = "BranchesNEW";
        
        // var_dump($this->branches);
        return Result::SUCCESS;
    }

    public function create()
    {
        try {
            $this->loadRepository('special');
        } catch(EmptyRepositoryException $exp) {
            $this->cloneUrlAction();
            return 'empty_repository';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }

        $form = $this->getAddBranchForm();
        if ($this->isPOST()) {
            $form->submit($_POST);

            if (!$form->validate()) {
                return Result::FORM;
            }


            $this->createdBranch = $form->branchname;

            $this->getGitDao()->getDb()->beginTransaction();
            try {
                $security = $this->getServices()->get('security');
                $user = $security->getUser();

                $push = new Push();
                $push->getRepository()->add($this->entity);
                $push->getAuthor()->add($user);
                $push->setCreatedOn(date('Y-m-d H:i:s'));

                $branch = new Reference();
                $branch->setName($form->branchname);
                $branch->setFullname('refs/heads/'. $form->branchname);
                $branch->setRepositoryId($this->entity->getId());
                $branch->setType(Reference::TYPE_BRANCH);
                $branch->setCommitHash($this->getGitDao()->getLastIndexedCommit($this->entity)->getHash());
                $branch->setCreatedOn(date('Y-m-d H:i:s'));

                $push->getReferences()->add($branch);

                $this->getGitDao()->savePush($push);

                $this->getGitService()->createBranch($this->entity, $user, $form->branchname, $this->branch);

                $this->getGitDao()->getDb()->commit();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp->getMessage();
                $this->getGitDao()->getDb()->rollBack();
                return Result::ERROR;
            }

            return Result::SUCCESS;
        }

        return Result::FORM;
    }

    public function createTag()
    {
        try {
            $this->loadRepository('special');
        } catch(EmptyRepositoryException $exp) {
            $this->cloneUrlAction();
            return 'empty_repository';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }

        $form = $this->getAddTagForm();
        if ($this->isPOST()) {
            $form->submit($_POST);

            if (!$form->validate()) {
                return Result::FORM;
            }


            $this->createdBranch = $form->tagname;

            $this->getGitDao()->getDb()->beginTransaction();
            try {
                $security = $this->getServices()->get('security');
                $user = $security->getUser();

                $push = new Push();
                $push->getRepository()->add($this->entity);
                $push->getAuthor()->add($user);
                $push->setCreatedOn(date('Y-m-d H:i:s'));

                $tag = new Reference();
                $tag->setName($form->tagname);
                $tag->setFullname('refs/tags/'. $form->tagname);
                $tag->setRepositoryId($this->entity->getId());
                $tag->setType(Reference::TYPE_TAG);
                $tag->setCommitHash($this->getGitDao()->getLastIndexedCommit($this->entity)->getHash());
                $tag->setCreatedOn(date('Y-m-d H:i:s'));

                $push->getReferences()->add($tag);

                $this->getGitDao()->savePush($push);

                $this->getGitService()->createTag($this->entity, $user, $form->tagname, $form->reference, $form->annotation);

                $this->getGitDao()->getDb()->commit();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp->getMessage();
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
            $this->loadRepository('special');
        } catch(EmptyRepositoryException $exp) {
            $this->cloneUrlAction();
            return 'empty_repository';
        } catch(\Exception $exp) {
            $this->errorMsg = $exp;
            return Result::ERROR;
        }


        if (empty($this->branch)) {
            $this->errorMsg = "No branch selected";
            return Result::FORM;
        } elseif ($this->branch == $this->entity->getDefault_branch()) {
            $this->errorMsg = "This is the repository's default branch";
            return Result::FORM;
        }

        try {
            $ref = $this->getGitDao()->findReference($this->entity, $this->branch);
            $this->refType = $ref->getType();
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::FORM;
        }

        $this->canDeleteBranch = true;

        $this->getGitDao()->getDb()->beginTransaction();
        if ($this->isPOST()) {
            try {
                $security = $this->getServices()->get('security');
                $user = $security->getUser();

                $this->getGitDao()->deleteReference($this->entity, $ref);

                if ($this->refType == Reference::TYPE_BRANCH) {
                    $this->getGitService()->deleteBranch($this->entity, $user, $ref->getName());

                    $this->getGitDao()->notify(new RepositoryBranchDeleteEvent(
                            $this->entity,
                            $user,
                            $ref,
                            $this->getServices())
                    );
                } elseif ($this->refType == Reference::TYPE_TAG) {
                    $this->getGitService()->deleteTag($this->entity, $user, $ref->getName());

                    $this->getGitDao()->notify(new RepositoryTagDeleteEvent(
                            $this->entity,
                            $user,
                            $ref,
                            $this->getServices())
                    );
                } else {
                    throw new \Exception('Invalid reference');
                }

                $this->getGitDao()->getDb()->commit();
            } catch(\Exception $exp) {
                $this->errorMsg = $exp->getMessage();
                $this->getGitDao()->getDb()->rollBack();
                return Result::ERROR;
            }

            // back to default branch
            $this->createdBranch = $this->entity->getDefault_branch();

            return Result::SUCCESS;
        }

        return Result::FORM;
    }

    public function getAddBranchForm()
    {
        if (!isset($this->addBranchForm)) {
            $this->addBranchForm = new AddBranchForm();
            $this->addBranchForm->setAction($this->getServices()->get('viewHelper')->url('AddBranch', array('name' => $this->name, 'branch' => $this->branch)));
        }
        return $this->addBranchForm;
    }

    public function getAddTagForm()
    {
        if (!isset($this->addTagForm)) {
            $this->addTagForm = new AddTagForm();
            $this->addTagForm->setAction($this->getServices()->get('viewHelper')->url('AddTag', array('name' => $this->name, 'branch' => $this->branch)));
            $this->addTagForm->element('reference')->setDefault($this->branch);
        }
        return $this->addTagForm;
    }

    public function getBranches()
    {
        return $this->branches;
    }
    
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return null
     */
    public function getCreatedBranch()
    {
        return $this->createdBranch;
    }

    /**
     * @return boolean
     */
    public function getCanDeleteBranch()
    {
        return $this->canDeleteBranch;
    }

    /**
     * @return string
     */
    public function getRefType()
    {
        return $this->refType;
    }
}