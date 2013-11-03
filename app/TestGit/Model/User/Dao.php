<?php
namespace TestGit\Model\User;

use TestGit\Model\Dao as DaoBase;
use Fwk\Security\User\Provider;
use Fwk\Security\Exceptions\UserNotFound;
use Fwk\Security\User;
use Fwk\Db\Connection;
use TestGit\StringUtils;
use Fwk\Db\Query;
use TestGit\Model\Tables;

class Dao extends DaoBase implements Provider
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
    
    const ENTITY_USER   = 'Forgery\\User\\User';
    
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
            'usersTable'    => Tables::USERS
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
                        ->from($this->get('usersTable'));

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
     * Sauvegarde un utilisateur (nouveau ou existant)
     * 
     * @param User $user Utilisateur à sauvegarder
     * 
     * @return boolean 
     */
    public function save(User $user)
    {
        return $this->getDb()->table($this->get('usersTable'))->save($user);
    }
    
    public function supports(User $user)
    {
        return ($user instanceof User);
    }
    
    public function create($username, $password, $email, \Traversable $roles = null)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setSlug(StringUtils::slugize($username));
        $user->setActive(1);
        $user->setDate_registration(date("Y-m-d H:i:s"));
        $user->setEmail($email);
        
        // generate password
        $generator  = UtilsFactory::newPasswordGenerator();
        $saltFunc   = UtilsFactory::newSaltClosure();
        $generator->setSalt($saltFunc($user));
        $user->setPassword($generator->create($password));
        
        $user->getRolesRelation()->addAll($roles);
        
        return $user;
    }
}