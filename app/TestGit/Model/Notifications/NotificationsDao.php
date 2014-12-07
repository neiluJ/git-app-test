<?php
namespace TestGit\Model\Notifications;

use Fwk\Db\Query;
use TestGit\Model\Dao;
use Fwk\Db\Connection;
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
     * Constructeur 
     * 
     * @param Connection $connection Connexion à la base de donnée
     * @param array      $options    Options de configuration
     * 
     * @return void
     */
    public function __construct(Connection $connection = null, UsersDao $usersDao,
        $options = array())
    {
        $options = array_merge(array(
            'notificationsTable'        => Tables::NOTIFICATIONS,
            'notificationsUsersTable'   => Tables::NOTIFICATIONS_USERS,
        ), $options);
        
        parent::__construct($connection, $options);

        $this->usersDao = $usersDao;
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

        $query->andWhere('channel IN (?)');
        $params[] = implode(', ', $channels);

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
        }

        return $channels;
    }
}