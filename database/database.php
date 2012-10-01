<?php

/**
 * Riff PHP Library
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  RiffDatabase
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

/**
 * The database used by Riff to avoid writing SQL queries
 *
 * @package     Riff
 * @subpackage  RiffDatabase
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

class RiffDatabase
{

    /**
     * Database name
     *
     * @var string
     */
    private $database;

    /**
     * Database driver. Riff currently only supports 'mysql'.
     *
     * @var string
     */
    private $driver;

    /**
     * Database handler PDO object
     *
     * @var PDO
     */
    private $handler;

    /**
     * Database host (when in doubt, use localhost)
     *
     * @var string
     */
    private $host;

    /**
     * The password to access the database
     *
     * @var string
     */
    private $password;

    /**
     * The database user
     *
     * @var string
     */
    private $username;

    /**
     * Creates a connection instance
     * 
     * @param string $database  The database you want to connect to
     * @param string $driver    The driver to use. Riff only supports HTML
     * @param string $host      The host or IP of the database server
     * @param string $username  Database user
     * @param string $password  Datapase password
     */
    public function __construct($database, $driver = 'mysql', $host = 'localhost', $username = 'root', $password = 'root')
    {
        $this->database = $database;
        $this->driver   = $driver;
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Connect to the database if we don't have a handler already 
     */ 
    private function connect()
    {
        if (!$this->handler) {

            try {

                // Create the DSN
                $dsn  = $this->driver . ':host=' . $this->host . ';dbname=' . $this->database;
                $dsn .= ';user=' . $this->username . ';password=' . $this->password;

                // Create the handler and configure it
                $this->handler = new PDO($dsn, $this->username, $this->password);
                $this->handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if ($this->driver == 'mysql') $this->handler->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            
            } catch (PDOException $e) {
                throw new RiffException('Riff could not connect to the database');
            }
        }
    }

    /**
     * Delete one or more rows from a table
     * 
     * @param string $table
     * @param string|array $where
     * @return int                  The affected rows
     */ 
    public function delete($table, $where)
    {
        $query = new RiffQuery('DELETE FROM :table');
        $query->addWhere($where);
        $query->addParameter('table', $table);

        try {
            return $this->execute($query)->rowCount();
        } catch (RiffException $e) {
            throw new RiffException('Could not delete from ' . $table);
        }
    }

    /**
     * Executes any given query
     * 
     * @param RiffQuery|string $query
     * @return PDOStatement
     */
    public function execute($query)
    {
        if (!$this->handler) $this->connect();

        $query = (is_string($query)) ? new RiffQuery($query) : $query;

        $statement = $this->handler->prepare($query->fetchQuery());

        // If the statement failed
        if ($statement === false) throw new RiffException('Something went wrong preparing query "' . $query . '"');

        // Bind PDO parameters
        $PDOparameters = $query->getPDOparameters();

        if (count($PDOparameters > 0)) {
            foreach ($PDOparameters as $parameter => $value) {
                $statement->bindValue(':' . (string) $parameter, $value, $this->getType($value));
            }   
        }

        try {
            $statement->execute();
            return $statement;
        } catch (PDOException $e) {
            throw new RiffException($e->getMessage());
        }
    }

    /**
     * Get the PDO type of a variable (think PARAM_INT for use in limits)
     * 
     * @param mixed $value
     * @return int
     */
    private function getType($value)
    {
        // Type is either string or integer
        $return = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;

        // Do a last null check
        return (is_null($value)) ? PDO::PARAM_NULL : $return;
    }

    
    /**
     * Easily insert an entry into a table
     * 
     * @param string $table
     * @param array $values     Key value pairs
     * @return int              The affected rows
     */ 
    public function insert($table, $values)
    {
        if (!$this->handler) $this->connect();

        if (!is_array($values)) throw new RiffException('No values to insert');


        $sql = "INSERT INTO :table (";

        foreach ($values as $column => $value) {
            $sql .= RiffFilter::sanitize($column) . ',';
        }

        $sql = rtrim($sql, ',');

        $sql .= ') VALUES (';

        foreach ($values as $column => $value) {
            $sql .= ':' . $column . ',';
        }

        $sql = rtrim($sql, ',');

        $sql .= ')';

        $query = new RiffQuery($sql, $values, array('table' => $table));

        try {
            return $this->execute($query)->rowCount();
        } catch (RiffException $e) {
            throw new RiffException('Could not insert data into ' . $table);
        }
        
    }

    /**
     * Allows speed writing of simple SELECT statements without complex JOINS
     * It allows implicit joins (table1, table2)
     * 
     * @param string $subject                   What to select
     * @param string $table                     What table to select from
     * @param string|array[optional] $where     Where-clause in a key => value way
     * @param int|array[optional] $limit        If int, just the limit. If array, [0] is start, [1] is count
     * @param string|array[optional] $orderBy   Allows to specify the column to order by [0] and the order method [1]
     * @param string[optional] $groupBy         Allows to specify the column to group by 
     * @return array           
     */
    public function select($subject, $table, $where = null, $limit = null, $orderBy = null, $groupBy = null)
    {
        if (!$this->handler) $this->connect();

        // Start the query
        $query = new RiffQuery('SELECT :subject FROM :table');
        $query->addParameters(array('subject' => (string) $subject, 'table' => (string) $table));

        // Include a WHERE clause if needed
        // if (isset($where)) $query->addWhere($where);
        if (isset($where)) $query->addWhere($where);

        // Allows to order the results
        if (isset($orderBy)) $query->addOrderBy($orderBy);

        // Allows to group the results
        if (is_string($groupBy)) $query->addGroupBy($groupBy);

        // Allows to set a limit on the rows selected
        if (isset($limit)) $query->addLimit($limit);

        $statement = $this->execute($query);

        $result = array();
        $temp = $statement->fetchAll(PDO::FETCH_ASSOC);

        // If an ID was selected, we'll put this as the key instead of numeric
        if (isset($temp[0]['id'])) {
            foreach ($temp as $row) {
                $result[$row['id']] = $row;
            }
        } else {
            $result = $temp;
        }

        return $result;
    }

    /**
     * Easily update one or more records
     * 
     * @param string $table
     * @param array $values
     * @param string|array $where
     * @return int
     */ 
    public function update($table, $values, $where)
    {
        if (!$this->handler) $this->connect();

        $sql = 'UPDATE :table SET ';

        foreach ($values as $column => $value) {
            $sql .= RiffFilter::sanitize((string) $column) . ' = ' . ':' . $column; 
        }

        $query = new RiffQuery($sql, $values, array('table' => $table));
        $query->addWhere($where);

        try {
            return $this->execute($query)->rowCount();
        } catch (RiffException $e) {
            throw new RiffException('Could not update ' . $table);
        }
    }
 
}