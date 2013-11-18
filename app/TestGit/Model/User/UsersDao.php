<?php
namespace TestGit\Model\User;

use TestGit\Model\Dao;
use Fwk\Security\User\Provider;
use Fwk\Security\Exceptions\UserNotFound;
use Fwk\Security\User;
use TestGit\Model\User\User as BaseUser;
use Fwk\Db\Connection;
use TestGit\StringUtils;
use Fwk\Db\Query;
use TestGit\Model\Tables;

class UsersDao extends Dao implements Provider
{
    /**
     * Constantes pour les types de recherches
     * 
     * @see findOne()
     */
    const FIND_USERNAME = 'username';
    const FIND_EMAIL    = 'email';
    const FIND_HASH     = 'hash';
    const FIND_ID       = 'id';
    const FIND_SLUG     = 'slug';
    
    const ENTITY_USER   = 'TestGit\\Model\\User\\User';
    
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
            'usersTable'    => Tables::USERS,
            'sshKeysTable'  => Tables::SSH_KEYS
        ), $options);
        
        parent::__construct($connection, $options);
    }
    
    /**
     *
     * @param string $identifier
     * 
     * @return SpokelaUser 
     */
    public function getById($identifier)
    {
        return $this->findOne($identifier, self::FIND_ID, true);
    }
    
    /**
     *
     * @param string  $userName
     * @param boolean $strict
     * @return User 
     */
    public function getByUsername($userName, $strict = true)
    {
        return $this->findOne($userName, self::FIND_USERNAME, true);
    }
    
    public function refresh(User $user)
    {
        return $user;
    }
    
    /**
     * Cherche un utilisateur sur un critère ($search) 
     * Retourne null si non trouvé
     * 
     * @param string  $text       Search text
     * @param string  $search     Search type (see class constants)
     * @param boolean $onlyActive Search only active accounts
     * 
     * @return User 
     */
    public function findOne($text, $search = self::FIND_USERNAME, 
        $onlyActive = false)
    {
        $query = Query::factory()
                        ->select()
                        ->from($this->getOption('usersTable'));

        $params = array($text);
        switch($search)
        {
            case self::FIND_USERNAME:
                $query->where('username = ?');
                break;
            
            case self::FIND_EMAIL:
                $query->where('email = ?');
                break;
            
            case self::FIND_HASH:
                $query->where('hash = ?');
                break;
            
            case self::FIND_ID:
                $query->where('id = ?');
                break;
            
            case self::FIND_SLUG:
                $query->where('slug = ?');
                break;
            
            default:
                throw new DaoException(
                    sprintf(
                        'Invalid search type "%s"', 
                        $search
                    )
                );
        }
        
        if($onlyActive === true) {
            $query->andWhere('active = 1');
        }
        
        $query->limit(1)
              ->entity(self::ENTITY_USER);
        
        $res = $this->getDb()->execute($query, $params);
        
        $user = (count($res) ? $res[0] : null);
        if (!$user instanceof User) {
            throw new UserNotFound();
        }
        
        return $user;
    }
    
    /**
     * @param boolean $onlyActive Search only active accounts
     * 
     * @return array 
     */
    public function findAll($onlyActive = false)
    {
        $query = Query::factory()
                        ->select()
                        ->entity(self::ENTITY_USER)
                        ->from($this->getOption('usersTable'));

        if($onlyActive === true) {
            $query->where('active = 1');
        }
        
        return $this->getDb()->execute($query);
    }
    
    public function findNonAuthorized($repoId, $onlyActive = true)
    {
        $query = Query::factory()
                        ->select()
                        ->entity(self::ENTITY_USER)
                        ->from($this->getOption('usersTable') .' u')
                        ->where("u.id NOT IN (SELECT user_id FROM ". Tables::ACCESSES ." WHERE repository_id = ?)");
                        
        if($onlyActive === true) {
            $query->andWhere('u.active = 1');
        }
        
        return $this->getDb()->execute($query, array($repoId));
    }
    
    /**
     * Sauvegarde un utilisateur (nouveau ou existant)
     * 
     * @param User $user Utilisateur à sauvegarder
     * 
     * @return boolean 
     */
    public function save(User $user)
    {
        return $this->getDb()->table($this->getOption('usersTable'))->save($user);
    }
    
    public function supports(User $user)
    {
        return ($user instanceof User);
    }
    
    public function create($username, $password, $email, array $roles = array())
    {
        $user = new BaseUser();
        $user->setUsername($username);
        $user->setSlug(StringUtils::slugize($username));
        $user->setActive(1);
        $user->setDate_registration(date("Y-m-d H:i:s"));
        $user->setEmail($email);
        
        // generate password
        $this->updatePassword($user, $password);
        $user->getRolesRelation()->addAll($roles);
        
        return $user;
    }
    
    public function updatePassword(User $user, $newPassword)
    {
        $generator  = UtilsFactory::newPasswordGenerator();
        $saltFunc   = UtilsFactory::newSaltClosure();
        $generator->setSalt($saltFunc($user));
        $user->setPassword($generator->create($newPassword));
        
        return $user;
    }
    
    public function findSshKeyByHash($hash)
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('sshKeysTable'))
                ->where('hash = ?')
                ->limit(1);
        
        return $this->getDb()->execute($query, array($hash));
    }
    
    public function findSshKeyByTitleUser($title, User $user)
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('sshKeysTable'))
                ->where('title = ? AND user_id = ?')
                ->limit(1);
        
        return $this->getDb()->execute($query, array($title, $user->getId()));
    }
    
    public function findSshKeyById($id)
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('sshKeysTable'))
                ->where('id = ?')
                ->limit(1);
        
        return $this->getDb()->execute($query, array($id));
    }
}