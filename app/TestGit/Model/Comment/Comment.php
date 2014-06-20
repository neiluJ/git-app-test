<?php
namespace TestGit\Model\Comment;

use Nitronet\Comments\Model\Comment as CommentBase;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Tables;
use TestGit\Model\User\UsersDao;
use Fwk\Db\Relation;
use Fwk\Db\Relations\One2One;

class Comment extends CommentBase
{
    protected $author;
    protected $authorId;

    protected $repository;
    protected $repositoryId;

    public function __construct()
    {
        $this->author = new One2One(
            'authorId',
            'id',
            Tables::USERS,
            UsersDao::ENTITY_USER
        );

        $this->author->setFetchMode(Relation::FETCH_LAZY);

        $this->repository = new One2One(
            'repositoryId',
            'id',
            Tables::REPOSITORIES,
            GitDao::ENTITY_REPO
        );

        $this->repository->setFetchMode(Relation::FETCH_LAZY);
    }

    /**
     * @return One2One
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $authorId
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @return One2One
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repositoryId
     */
    public function setRepositoryId($repositoryId)
    {
        $this->repositoryId = $repositoryId;
    }

    /**
     * @return mixed
     */
    public function getRepositoryId()
    {
        return $this->repositoryId;
    }

    public function isCommitComment()
    {
        return (strpos($this->thread, 'commit-', 0) !== false);
    }

    public function isCompareComment()
    {
        return (strpos($this->thread, 'compare-', 0) !== false);
    }

    public function getCommitHash()
    {
        if (!$this->isCommitComment()) {
            return null;
        }

        return substr($this->thread, strlen('commit-'. $this->id .'-'));
    }
}