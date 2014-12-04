<?php
namespace TestGit\Model\Notifications;

use Fwk\Db\Relation;
use Fwk\Db\Relations\Many2Many;
use Fwk\Db\Relations\One2Many;
use Fwk\Db\Relations\One2One;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Tables;
use TestGit\Model\User\UsersDao;

class Notification
{
    const TYPE_MENTION          = 'mention';        // channels: chat / repository
    const TYPE_PULLREQUEST      = 'pullrequest';    // channels: repository / organization
    const TYPE_BRANCH           = 'branch';         // channels: repository / organization
    const TYPE_TAG              = 'tag';            // channels: repository / organization
    const TYPE_ACCREDITATION    = 'accreditation';  // channels: general / organization
    const TYPE_USER_ADD         = 'adduser';        // channels: admin / organization
    const TYPE_USER_REMOVE      = 'deluser';        // channels: admin / organization
    const TYPE_APPLICATION      = 'application';    // channels: admin
    const TYPE_FAILED_LOGIN     = 'failedlogin';    // channels: general

    const CHANNEL_GENERAL       = 'general';
    const CHANNEL_ADMIN         = 'admin';
    const CHANNEL_REPOSITORY    = 'repository';
    const CHANNEL_ORGANIZATION  = 'organization';
    const CHANNEL_CHAT          = 'chat';

    protected $id;
    protected $channel;
    protected $authorId;
    protected $authorName;
    protected $type;
    protected $repositoryId;
    protected $repositoryName;
    protected $text;
    protected $target;
    protected $createdOn;

    protected $author;
    protected $repository;
    protected $users;

    public function __construct()
    {
        $this->author = new One2One(
            'userId',
            'id',
            Tables::USERS,
            UsersDao::ENTITY_USER
        );

        $this->repository = new One2One(
            'repositoryId',
            'id',
            Tables::REPOSITORIES,
            GitDao::ENTITY_REPO
        );

        $this->users = new One2Many(
            'id',
            'notificationId',
            Tables::NOTIFICATIONS_USERS
        );
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->authorId;
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
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param mixed $authorName
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getRepositoryId()
    {
        return $this->repositoryId;
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
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * @param mixed $repositoryName
     */
    public function setRepositoryName($repositoryName)
    {
        $this->repositoryName = $repositoryName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return One2Many
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return One2One
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return One2One
     */
    public function getRepository()
    {
        return $this->repository;
    }
}