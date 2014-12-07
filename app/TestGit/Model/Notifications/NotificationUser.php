<?php
namespace TestGit\Model\Notifications;

use Fwk\Db\Relation;
use Fwk\Db\Relations\One2One;
use TestGit\Model\Tables;
use TestGit\Model\User\UsersDao;

class NotificationUser
{
    protected $notificationId;
    protected $userId;
    protected $dateRead;

    protected $notification;
    protected $user;

    public function __construct()
    {
        $this->notification = new One2One(
            'notificationId',
            'id',
            Tables::NOTIFICATIONS,
            NotificationsDao::ENTITY_NOTIFICATION
        );
        $this->notification->setFetchMode(Relation::FETCH_EAGER);

        $this->user = new One2One(
            'userId',
            'id',
            Tables::USERS,
            UsersDao::ENTITY_USER
        );
    }

    /**
     * @param \Fwk\Db\Relations\One2One $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return \Fwk\Db\Relations\One2One
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param mixed $notificationId
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * @return mixed
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @param \Fwk\Db\Relations\One2One $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Fwk\Db\Relations\One2One
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }


    /**
     * @param mixed $dateRead
     */
    public function setDateRead($dateRead)
    {
        $this->dateRead = $dateRead;
    }

    /**
     * @return mixed
     */
    public function getDateRead()
    {
        return $this->dateRead;
    }

    public function isUnread()
    {
        return ($this->dateRead === null);
    }
}