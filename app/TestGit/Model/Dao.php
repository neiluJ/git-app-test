<?php
namespace TestGit\Model;

use Fwk\Db\Connection;
use Fwk\Events\Dispatcher;

abstract class Dao extends Dispatcher
{
    /**
     * Connexion à la base de donnée
     * 
     * @var Connection 
     */
    protected $db;
    
    /**
     * Options
     * 
     * @var array
     */
    protected $options = array();
    
    /**
     * Constructeur 
     * 
     * @param Connection $connection Connexion à la base de donnée
     * @param array      $options    Options de configuration
     * 
     * @return void
     */
    public function __construct(Connection $connection = null, 
        array $options = array())
    {
        $this->db = $connection;
        $this->options = $options;
    }
    
    /**
     * Retourne la connexion à la base de donnée
     * 
     * @throws DaoException si connection non définie
     * 
     * @return Connection l'objet de connextion
     */
    public function getDb()
    {
        if (!isset($this->db)) {
            throw new DaoException(
                sprintf('Database has not been initialized for this Dao')
            );
        }
        
        return $this->db;
    }

    /**
     * Défini la connexion à la base de donnée
     * 
     * @param Connection $db Database connection
     * 
     * @return void
     */
    public function setDb(Connection $db)
    {
        $this->db = $db;
    }
    
    /**
     *
     * @param string $opt
     * @param mixed $default
     * 
     * @return mixed
     */
    public function getOption($opt, $default = false)
    {
        return (array_key_exists($opt, $this->options) ? 
            $this->options[$opt] : 
            $default
        );
    }
    
    /**
     *
     * @param string $opt
     * @param mixed $value
     * 
     * @return Dao 
     */
    public function setOption($opt, $value)
    {
        $this->options[$opt] = $value;
        
        return $this;
    }
}