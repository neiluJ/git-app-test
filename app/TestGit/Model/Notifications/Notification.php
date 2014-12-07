<?php
namespace TestGit\Model\Notifications;

use Fwk\Db\EventSubscriber;
use Fwk\Db\Listeners\Typable;
use Fwk\Db\Relations\One2Many;
use Fwk\Db\Relations\One2One;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Tables;
use TestGit\Model\User\UsersDao;

class Notification implements EventSubscriber
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
    const CHANNEL_REPOSITORY    = 'repo:';
    const CHANNEL_ORGANIZATION  = 'org:';
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
            Tables::NOTIFICATIONS_USERS,
            NotificationsDao::ENTITY_NOTIFICATION_USER
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

    public function getIcon()
    {
        switch($this->type)
        {
            case self::TYPE_ACCREDITATION:
            case self::TYPE_APPLICATION:
            case self::TYPE_BRANCH:
            case self::TYPE_FAILED_LOGIN:
                return 'octicon octicon-x';

            case self::TYPE_MENTION:
                return 'octicon octicon-mention';

            case self::TYPE_PULLREQUEST:
            case self::TYPE_TAG:
            case self::TYPE_USER_ADD:

            case self::TYPE_USER_REMOVE:
                return 'octicon octicon-mention';

            default:
                return null;
        }
    }

    /**
     * Returns a list of listeners.
     * Listeners can be real objects (@see Listeners directory) or callables, using
     * the array key as the event's name:
     *
     * <pre>
     * array(
     *      'afterSave' => array($this, 'callableFunc'),
     *      new Listener()
     * );
     * </pre>
     *
     * @return array
     */
    public function getListeners()
    {
        return array(
            new Typable()
        );
    }


}