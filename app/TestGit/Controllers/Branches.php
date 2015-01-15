<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Fwk\Form\Validation\EqualsFilter;
use TestGit\EmptyRepositoryException;
use TestGit\Form\AddBranchForm;
use TestGit\Model\Git\Push;
use TestGit\Model\Git\Reference;

class Branches extends Repository
{
    protected $branches = array();
    protected $tags     = array();
    protected $addBranchForm;
    protected $createdBranch = null;

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
            $this->loadRepository('read');
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

                $this->getGitService()->createBranch($this->entity, $form->branchname, $this->branch);

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

    public function getAddBranchForm()
    {
        if (!isset($this->addBranchForm)) {
            $this->addBranchForm = new AddBranchForm();
            $this->addBranchForm->setAction($this->getServices()->get('viewHelper')->url('AddBranch', array('name' => $this->name, 'branch' => $this->branch)));
        }
        return $this->addBranchForm;
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
}