<?php
namespace TestGit\Model\Notifications;

use Fwk\Db\Query;
use TestGit\Model\Dao;
use Fwk\Db\Connection;
use TestGit\Model\Git\GitDao;
use TestGit\Model\Tables;
use TestGit\Model\User\User;
use TestGit\Model\User\UsersDao;

class NotificationsDao extends Dao
{
    const ENTITY_NOTIFICATION   = 'TestGit\Model\Notifications\Notification';
    const ENTITY_NOTIFICATION_USER   = 'TestGit\Model\Notifications\NotificationUser';

    /**
     * @var \TestGit\Model\User\UsersDao
     */
    protected $usersDao;

    /**
     * @var \TestGit\Model\Git\GitDao
     */
    protected $gitDao;

    /**
     * Constructeur 
     * 
     * @param Connection $connection Connexion à la base de donnée
     * @param array      $options    Options de configuration
     * 
     * @return void
     */
    public function __construct(Connection $connection = null, UsersDao $usersDao,
        GitDao $gitDao, $options = array()
    ) {
        $options = array_merge(array(
            'notificationsTable'        => Tables::NOTIFICATIONS,
            'notificationsUsersTable'   => Tables::NOTIFICATIONS_USERS,
        ), $options);
        
        parent::__construct($connection, $options);

        $this->usersDao = $usersDao;
        $this->gitDao   = $gitDao;
    }
    
    public function getForUser(User $user, array $channels)
    {
        $query = Query::factory()
            ->select()
            ->from($this->getOption('notificationsUsersTable', Tables::NOTIFICATIONS_USERS))
            ->entity(NotificationsDao::ENTITY_NOTIFICATION_USER)
            ->where('userId = ?')
            ->orderBy('notifications.createdOn', 'desc');

        $params = array($user->getId());

        if (!count($channels)) {
            throw new \Exception('You must choose at least one channel');
        }

        $chans = $this->getChannelsForUser($user);
        foreach ($channels as $chan) {
            if (!array_key_exists($chan, $chans)) {
                throw new \Exception('Invalid or No Access to channel: '. $chan);
            }
        }

        $andw = 'channel IN (';
        foreach ($channels as $chan) {
            $andw .= '?,';
            $params[] = $chan;
        }
        $query->andWhere(rtrim($andw,',').')');

        return $this->getDb()->execute($query, $params);
    }

    public function getChannelsForUser(User $user)
    {
        $channels = array(
            Notification::CHANNEL_GENERAL   => 'General',
        );

        $orgs = $this->usersDao->getUserOrganizations($user);
        foreach ($orgs as $orga) {
            $channels[Notification::CHANNEL_ORGANIZATION . $orga->getSlug()] = $orga->getUsername();
            foreach ($orga->getRepositories() as $repo) {
                $channels[Notification::CHANNEL_REPOSITORY . $repo->getFullname()] = $repo->getFullname();
            }
        }

        $repos = $this->gitDao->getRepositoriesForUser($user);
        foreach ($repos as $repo) {
            $channels[Notification::CHANNEL_REPOSITORY . $repo->getFullname()] = $repo->getFullname();
        }

        return $channels;
    }

    public function getNotificationsCount(User $user, array $channels = array())
    {
        $query = Query::factory()
            ->select('channel, count(*) as counter')
            ->from($this->getOption('notificationsUsersTable', Tables::NOTIFICATIONS_USERS))
            ->join('notifications', 'notificationId', 'id', Query::JOIN_INNER)
            ->setFetchMode(\PDO::FETCH_CLASS)
            ->where('userId = ? AND dateRead IS NULL')
            ->groupBy('channel');

        if (!count($channels)) {
            $channels = $this->getChannelsForUser($user);
        }

        $params[] = $user->getId();
        $andw = 'channel IN (';
        foreach ($channels as $chan) {
            $andw .= '?,';
            $params[] = $chan;
        }
        $query->andWhere(rtrim($andw,',').')');

        $res = $this->getDb()->execute($query, $params);
        $results = array();
        $total = 0;
        foreach ($res as $result) {
            $results[$result->channel] = (int)$result->counter;
            $total += (int)$result->counter;
        }

        $results['__total'] = $total;

        return $results;
    }

    public function read(User $user, $nId)
    {
        $query = Query::factory()
            ->update($this->getOption('notificationsUsersTable', Tables::NOTIFICATIONS_USERS))
            ->where('userId = ? AND notificationId = ?')
            ->set('dateRead', 'CASE WHEN dateRead IS NULL THEN NOW() WHEN dateRead IS NOT NULL THEN NULL END');

        $this->getDb()->execute($query, array($user->getId(), $nId));
    }

    public function readAll(User $user, $channel)
    {
        $query = Query::factory()
            ->update($this->getOption('notificationsUsersTable', Tables::NOTIFICATIONS_USERS) .', '. $this->getOption('notificationsTable', Tables::NOTIFICATIONS))
            ->where('notifications_users.notificationId = notifications.id AND userId = ? AND channel = ? AND dateRead IS NULL')
            ->set('dateRead', 'NOW()');

        $this->getDb()->execute($query, array($user->getId(), $channel));
    }

    public function delete(User $user, $nId)
    {
        $query = Query::factory()
            ->delete($this->getOption('notificationsUsersTable', Tables::NOTIFICATIONS_USERS))
            ->where('userId = ? AND notificationId = ?');

        $this->getDb()->execute($query, array($user->getId(), $nId));
    }

    public function deleteAll(User $user, $channel)
    {
        $query = Query::factory()
            ->delete($this->getOption('notificationsUsersTable', Tables::NOTIFICATIONS_USERS))
            ->where('userId = ? AND notificationId IN (SELECT id FROM notifications WHERE channel = ?)');

        $this->getDb()->execute($query, array($user->getId(), $channel));
    }
}