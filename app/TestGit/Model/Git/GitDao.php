<?php
namespace TestGit\Model\Git;

use TestGit\Model\Dao as DaoBase;
use Fwk\Db\Connection;
use Fwk\Db\Query;
use TestGit\Model\Tables;
use TestGit\Model\User\User;
use \Exception as RepositoryNotFound;
use TestGit\Model\Git\Repository;
use TestGit\Model\Git\Commit;
use TestGit\Model\Git\Push;

class GitDao extends DaoBase
{
    /**
     * Constantes pour les types de recherches
     * 
     * @see findOne()
     */
    const FIND_NAME     = 'name';
    const FIND_ID       = 'id';
    const FIND_OWNER    = 'owner';
    const FIND_FULLNAME = 'fullname';
    
    const FIND_COMMIT_HASH  = 'hash';
    const FIND_COMMIT_MSG   = 'message';
    const FIND_COMMIT_BOTH  = 'hash+msg';
    
    const ENTITY_REPO   = 'TestGit\\Model\\Git\\Repository';
    const ENTITY_ACCESS = 'TestGit\\Model\\Git\\Access';
    const ENTITY_PUSH   = 'TestGit\\Model\\Git\\Push';
    const ENTITY_COMMIT = 'TestGit\\Model\\Git\\Commit';
    const ENTITY_REFERENCE = 'TestGit\\Model\\Git\\Reference';
    
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
            'accessesTable'         => Tables::ACCESSES,
            'commitsTable'          => Tables::COMMITS,
            'referencesTable'       => Tables::REFERENCES,
            'pushesTable'           => Tables::PUSHES,
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
    public function findMany($text, $search = self::FIND_ID, $limit = 100, $includePublics = false)
    {
        $query = Query::factory()
                        ->select()
                        ->from($this->getOption('repositoriesTable'))
                        ->where('1 = 1');

        if (!is_array($text)) {
            $params = array($text);
        } else {
            $params = array($text[0], $text[1]);
        } 
        
        switch($search)
        {
            case self::FIND_OWNER:
                if (count($params) == 1) {
                    $query->andWhere('owner_id = ?');
                } elseif (count($params) == 2) {
                    $query->andWhere('(owner_id = ? AND name = ?)');
                }
                break;
            
            case self::FIND_ID:
                $query->andWhere('id = ?');
                break;
            
            case self::FIND_NAME:
                $query->andWhere('name = ?');
                break;
            
            default:
                throw new DaoException(
                    sprintf(
                        'Invalid search type "%s"', 
                        $search
                    )
                );
        }

        if ($includePublics == true) {
            $query->orWhere('public = 1');
        }

        $query->entity(self::ENTITY_REPO)
              ->orderBy('last_commit_date', false);
        
        return $this->getDb()->execute($query, $params);
    }

    /**
     *
     * @return \Fwk\Db\ResultSet;
     */
    public function findForks(Repository $repository)
    {
        $query = Query::factory()
            ->select()
            ->from($this->getOption('repositoriesTable'))
            ->where('parent_id = ?')
            ->entity(self::ENTITY_REPO)
            ->orderBy('last_commit_date', false);

        return $this->getDb()->execute($query, array($repository->getId()));
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
                        ->from($this->getOption('repositoriesTable'), 'r');

        if ($search !== self::FIND_OWNER) {
            $params = array($text);
        } elseif (is_array($text)) {
            $params = array($text[0], $text[1]);
        }
        
        switch($search)
        {
            case self::FIND_OWNER:
                $query->where('r.owner_id = ?');
                $query->andWhere('r.name = ?');
                break;
            
            case self::FIND_ID:
                $query->where('r.id = ?');
                break;
            
            case self::FIND_NAME:
                $query->where('r.name = ?');
                break;
            
            case self::FIND_FULLNAME:
                $query->where('r.fullname = ?');
                break;
            
            default:
                throw new DaoException(
                    sprintf(
                        'Invalid search type "%s"', 
                        $search
                    )
                );
        }
        
        $query->entity(self::ENTITY_REPO);
        
        $res = $this->getDb()->execute($query, $params);
        
        $repo = (count($res) ? $res[0] : null);
        if (!$repo instanceof Repository) {
            throw new RepositoryNotFound('Repository not found: '. $text);
        }
        
        return $repo;
    }
    
    /**
     * 
     * 
     * @return \Fwk\Db\ResultSet; 
     */
    public function findAll()
    {
        $query = Query::factory()
                        ->select()
                        ->from($this->getOption('repositoriesTable'));

        $query->entity(self::ENTITY_REPO)
              ->orderBy('last_commit_date', false);
        
        return $this->getDb()->execute($query);
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
     * @param Repository $repo 
     * 
     * @return boolean 
     */
    public function delete(Repository $repo)
    {
        return $this->getDb()
                    ->table($this->getOption('repositoriesTable'))
                    ->delete($repo);
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
        $type = self::TYPE_REPOSITORY, Repository $parent = null, $defaultBranch = 'master'
    ) {
        $repo = new Repository();
        $repo->setCreated_at(date('Y-m-d H:i:s'));
        $repo->setDescription($description);
        $repo->setName($repoName);
        $repo->setOwner_id($owner->getId());
        $repo->setPublic((bool)$isPublic);
        $repo->setType($type);
        $repo->setPath($this->getRepositoryPath($owner, $repoName));
        $repo->setFullname(sprintf('%s/%s', $owner->getUsername(), $repoName));
        $repo->setDefault_branch($defaultBranch);
        
        if ($parent instanceof Repository) {
            $repo->setParent_id($parent->getId());
        }
        
        $access = new Access();
        $access->setAdminAccess(true);
        $access->setReadAccess(true);
        $access->setWriteAccess(true);
        $access->setSpecialAccess(true);
        $access->setUser_id($owner->getId());
        
        $repo->getAccesses()->add($access);
        
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
    
    
    public function getRepositoryAccesses($repoId)
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('accessesTable'))
                ->entity(self::ENTITY_ACCESS)
                ->where('repository_id = ?');
        
        return $this->getDb()->execute($query, array($repoId));
    }
    
    public function addRepositoryAccess($repoId, $userId, $read = true, 
        $write = true, $special = false, $admin = false)
    {
        $access = new Access();
        $access->setRepository_id($repoId);
        $access->setUser_id($userId);
        $access->setReadAccess((int)$read);
        $access->setWriteAccess((int)$write);
        $access->setSpecialAccess((int)$special);
        $access->setAdminAccess((int)$admin);
        
        return $this->getDb()
                    ->table($this->getOption('accessesTable'))
                    ->save($access);
    }
    
    public function removeRepositoryAccess($repoId, $userId)
    {
        $query = Query::factory()
                ->delete($this->getOption('accessesTable'))
                ->where('user_id = ? AND repository_id = ?')
                ->limit(1);
        
        return $this->getDb()->execute($query, array($userId, $repoId));
    }
    
    /**
     * 
     * @param Access $access
     * 
     * @return boolean 
     */
    public function saveAccess(Access $access)
    {
        return $this->getDb()
                    ->table($this->getOption('accessesTable'))
                    ->save($access);
    }
    
    /**
     *
     * @param Repository $repository 
     * 
     * @return Commit
     */
    public function getLastIndexedCommit(Repository $repository)
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('commitsTable'))
                ->entity(self::ENTITY_COMMIT)
                ->where('repositoryId = ?')
                ->orderBy('authorDate', 'desc')
                ->limit(1);
        
        $res = $this->getDb()->execute($query, array($repository->getId()));
        
        return (count($res) ? $res[0] : null);
    }
    
    public function getAllReferences(Repository $repository)
    {
        $query = Query::factory()
                ->select()
                ->from($this->getOption('referencesTable'))
                ->entity(self::ENTITY_REFERENCE)
                ->where('repositoryId = ?');
        
        return $this->getDb()->execute($query, array($repository->getId()));
    }
    
    public function savePush(Push $push)
    {
        return $this->getDb()->table($this->getOption('pushesTable'))->save($push);
    }
    
    public function saveCommit(Commit $commit)
    {
        return $this->getDb()->table($this->getOption('commitsTable'))->save($commit);
    }
    
    public function saveReference(Reference $ref)
    {
        return $this->getDb()->table($this->getOption('referencesTable'))->save($ref);
    }
    
    public function findCommits($text, $type = self::FIND_COMMIT_BOTH, 
        User $user = null
    ) {
         $queryRepos = Query::factory()
                ->select()
                ->from($this->getOption('repositoriesTable'), 'r')
                ->where('1 = 1')
                ->entity(self::ENTITY_REPO);
         
        if ($user !== null) {
            $ids = array();
            foreach ($user->getAccesses() as $access) {
                $ids[] = $access->getRepository_id();
            }
            $queryRepos->andWhere('r.id IN ('. implode(',', $ids) .')');
            $queryRepos->orWhere('r.public = 1');
        } else {
            $queryRepos->andWhere('r.public = 1');
        }
        
        $repos = $this->getDb()->execute($queryRepos);
        
        $query = Query::factory()
                ->select()
                ->from($this->getOption('commitsTable'))
                ->entity(self::ENTITY_COMMIT)
                ->orderBy('committerDate', 'desc')
                ->limit(8)
                ;
        
        $params = array();
        switch($type)
        {
            case self::FIND_COMMIT_BOTH:
                $query->where('hash LIKE ? OR message LIKE ?');
                $params[]   = $text . '%';
                $params[]   = '%'. str_replace(' ', '%', $text) .'%';
                break;
            
            case self::FIND_COMMIT_HASH:
                $query->where('hash LIKE ?'); 
                $params[]   = $text . '%';
                break;
            
            case self::FIND_COMMIT_MSG:
                $query->where('message LIKE ?'); 
                $params[]   = '%'. str_replace(' ', '%', $text) .'%';
                break;
        }
        
        $ids = array();
        foreach ($repos as $rep) {
            $ids[] = $rep->getId();
        }
        
        $query->andWhere('repositoryId IN ('. implode(', ', $ids) .')');
        
        return $this->getDb()->execute($query, $params);
    }
    
    public function countCommits(User $user)
    {
        $query = Query::factory()
                ->select('COUNT(*) as count')
                ->from($this->getOption('commitsTable'))
                ->where('committerId = ?')
                ->setFetchMode(\PDO::FETCH_CLASS)
                ;
        
        $res = $this->getDb()->execute($query, array($user->getId()));

        return $res[0]->count;
    }
    
    public function getActivity(array $repositories, User $user = null, $limit = 20)
    {
        $params = array();
        $query = Query::factory()
                ->select()
                ->from($this->getOption('pushesTable'))
                ->entity(self::ENTITY_PUSH)
                ->orderBy('createdOn', 'desc')
                ->where('1 = 1');
        
        if (null !== $limit) {
            $query->limit($limit);
        }
        
        if ($user instanceof User) {
            $query->andWhere('userId = ?');
            $params[] = $user->getId();
        }
        
        $ids = array();
        foreach ($repositories as $rep) {
            $ids[] = $rep->getId();
        }
        
        $query->andWhere('repositoryId IN ('. implode(', ', $ids) .')');
        
        return $this->getDb()->execute($query, $params);
    }
}