<?php
namespace TestGit\Model\Git;

use TestGit\Model\Dao as DaoBase;
use Fwk\Db\Connection;
use Fwk\Db\Query;
use TestGit\Model\Tables;
use TestGit\Model\User\User;
use \Exception as RepositoryNotFound;

class Dao extends DaoBase
{
    /**
     * Constantes pour les types de recherches
     * 
     * @see findOne()
     */
    const FIND_NAME     = 'name';
    const FIND_ID       = 'id';
    const FIND_OWNER    = 'owner';
    
    const ENTITY_REPO   = 'TestGit\\Model\\Git\\Repository';
    
    const TYPE_REPOSITORY   = 'repository';
    const TYPE_FORK         = 'fork';
    
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
            'repositoriesTable'     => Tables::REPOSITORIES,
            'repositoriesBasePath'  => '/home/git/repositories'
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
        return $this->findOne($identifier, self::FIND_ID);
    }
    
    /**
     *
     * @param string  $userName
     * @param boolean $strict
     * @return User 
     */
    public function getByOwnerAndRepoName(User $owner, $repoName)
    {
        return $this->findOne(array($owner->getId(), $repoName), self::FIND_OWNER);
    }
    
    /**
     * 
     * @param mixed   $text       Search text
     * @param string  $search     Search type (see class constants)
     * 
     * @return \Fwk\Db\ResultSet; 
     */
    public function findMany($text, $search = self::FIND_ID, $limit = 100)
    {
        $query = Query::factory()
                        ->select()
                        ->from($this->getOption('repositoriesTable'));

        if ($search !== self::FIND_OWNER) {
            $params = array($text);
        } elseif (is_array($text)) {
            $params = array($text[0], $text[1]);
        } elseif ($text instanceof User) {
            $params = array($text->getId());
        }
        
        switch($search)
        {
            case self::FIND_OWNER:
                $query->where('owner_id = ?');
                if (count($params) == 2) {
                    $query->andWhere('name = ?');
                }
                break;
            
            case self::FIND_ID:
                $query->where('id = ?');
                break;
            
            case self::FIND_NAME:
                $query->where('name = ?');
                break;
            
            default:
                throw new DaoException(
                    sprintf(
                        'Invalid search type "%s"', 
                        $search
                    )
                );
        }
        
        $query->entity(self::ENTITY_REPO)
              ->limit($limit)
              ->orderBy('last_commit_date', false);
        
        $res = $this->getDb()->execute($query, $params);
        
        return $res;
    }
    
    /**
     * 
     * @param mixed   $text       Search text
     * @param string  $search     Search type (see class constants)
     * 
     * @throws RepositoryNotFound
     * @return Repository 
     */
    public function findOne($text, $search = self::FIND_ID)
    {
        $query = Query::factory()
                        ->select()
                        ->from($this->getOption('repositoriesTable'));

        if ($search !== self::FIND_OWNER) {
            $params = array($text);
        } elseif (is_array($text)) {
            $params = array($text[0], $text[1]);
        }
        
        switch($search)
        {
            case self::FIND_OWNER:
                $query->where('owner_id = ?');
                $query->andWhere('name = ?');
                break;
            
            case self::FIND_ID:
                $query->where('id = ?');
                break;
            
            case self::FIND_NAME:
                $query->where('name = ?');
                break;
            
            default:
                throw new DaoException(
                    sprintf(
                        'Invalid search type "%s"', 
                        $search
                    )
                );
        }
        
        $query->limit(1)
              ->entity(self::ENTITY_REPO);
        
        $res = $this->getDb()->execute($query, $params);
        
        $repo = (count($res) ? $res[0] : null);
        if (!$repo instanceof Repository) {
            throw new RepositoryNotFound();
        }
        
        return $repo;
    }
    
    /**
     * 
     * @param Repository $repo 
     * 
     * @return boolean 
     */
    public function save(Repository $repo)
    {
        return $this->getDb()
                    ->table($this->getOption('repositoriesTable'))
                    ->save($repo);
    }
    
    /**
     *
     * @param User   $owner       Repository owner
     * @param string $repoName    Repo name
     * @param string $description Repo's description
     * @param string $isPublic    public or private
     * @param string $type        repository or fork
     * 
     * @return Repository 
     */
    public function create(User $owner, $repoName, $description, $isPublic, 
        $type = self::TYPE_REPOSITORY, Repository $parent = null
    ) {
        $repo = new Repository();
        $repo->setCreated_at(date('Y-m-d H:i:s'));
        $repo->setDescription($description);
        $repo->setName($repoName);
        $repo->setOwner_id($owner->getId());
        $repo->setPublic((bool)$isPublic);
        $repo->setType($type);
        $repo->setPath($this->getRepositoryPath($owner, $repoName));
        
        if ($parent instanceof Repository) {
            $repo->setParent_id($parent->getId());
        }
        
        return $repo;
    }
    
    public function getRepositoryPath(User $owner, $repoName, $full = false)
    {
        $final = sprintf(
            "%s/%s.git",
            $owner->getSlug(),
            $repoName
        );
        
        if ($full === true) {
            $base = rtrim($this->getOption('repositoriesBasePath'), DIRECTORY_SEPARATOR);
            $final = $base . DIRECTORY_SEPARATOR . $final;
        }
        
        return $final;
    }
}