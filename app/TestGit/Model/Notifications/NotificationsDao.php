<?php
namespace TestGit\Model\Notifications;

use TestGit\Model\Dao;
use Fwk\Security\User;
use Fwk\Db\Connection;
use TestGit\Model\Tables;

class NotificationsDao extends Dao
{
    const ENTITY_NOTIFICATION   = 'TestGit\Model\Notifications\Notification';

    /**
     * Constructeur 
     * 
     * @param Connection $connection Connexion à la base de donnée
     * @param array      $options    Options de configuration
     * 
     * @return void
     */
    public function __construct(Connection $connection = null, 
        $options = array())
    {
        $options = array_merge(array(
            'notificationsTable'        => Tables::NOTIFICATIONS,
            'notificationsUsersTable'   => Tables::NOTIFICATIONS_USERS,
        ), $options);
        
        parent::__construct($connection, $options);
    }
    

}