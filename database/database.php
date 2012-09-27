<?php

require_once '../exception/exception.php';

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
     * Executes any given query with any parameters
     * 
     * @param string $query
     * @param array[optional] $parameters   The parameters to replace in the query, key => value
     */
    public function execute($query, $parameters = array())
    {
        if (!$this->handler) $this->connect();

        $statement = $this->handler->prepare((string) $query);

        // If the statement failed
        if ($statement === false) throw new RiffException('Something went wrong preparing query "' . $query . '"');

        // Bind parameters
        foreach ($parameters as $parameter => $value) {
            $statement->bindValue($parameter, $value, $this->getType($value));
        }

        if ($statement->execute()) {
            return $statement;
        } else {
            throw new RiffException('Something went wrong executing query "' . $query . '"');
        }
    }

    /**
     * Get the PDO type of a variable (think PARAM_INT for use in limits)
     * 
     * @param mixed $value
     * 
     * @return int
     */
    private function getType($value)
    {
        // Type is either string or integer
        $return = (is_int($value) || is_float($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;

        // Do a last null check
        return (is_null($value)) ? PDO::PARAM_NULL : $return;
    }

    /**
     * Allows speed writing of simple SELECT statements without complex JOINS
     * It allows implicit joins (table1, table2)
     * 
     * @param string $subject               What to select
     * @param string $table                 What table to select from
     * @param array $where                  Where-clause in a key => value way
     * @param int|array[optional] $limit    If int, just the limit. If array, [0] is start, [1] is count
     * @param string[optional] $orderBy     Allows to specify the column to order by
     * @param string[optional] $orderMethod ASC or DESC
     * @param string[optional] $groupBy     Allows to specify the column to group by            
     */
    public function select($subject, $table, $where = array(), $limit = null, $orderBy = '', $orderMethod = 'ASC', $groupBy = '')
    {
        if (!$this->handler) $this->connect();

        // Start the query and initiate the parameters
        $query = 'SELECT :subject FROM :table';
        $parameters = array('subject' => (string) $subject, 'table' => (string) $table);

        // Include a WHERE clause if needed
        if (count($where) > 0) {
            $query .= ' WHERE ';

            foreach ($where as $key => $value) {
                $query .= (string) $key . ' = :' . (string) $key . ',';
            }

            $query = rtrim($query, ',');

        }

        // Allows to order the results
        if (strlen($orderBy) > 2) {
            $allowedMethods = array('ASC', 'DESC');
            $orderMethod = strtoupper((string)$orderMethod);

            $query .= ' ORDER BY :orderBy :orderMethod';

            $parameters['orderBy'] = $orderBy;
            $parameters['orderMethod'] = (in_array($orderMethod, $allowedMethods)) ? $orderMethod : 'ASC';
        }

        // Allows to group the results
        if (strlen($groupBy) > 2) {
            $query .= ' GROUP BY :groupBy';
            $parameters['groupBy'] = (string) $groupBy;
        }


        // Allows to set a limit on the rows selected
        if (isset($limit)) {
            $query .= ' LIMIT ';

            if (is_int($limit)) {
                $query .= ':limit';
                $parameters['limit'] = (int) $limit;
            } else if (is_array($limit)) {
                $query .= ':limitStart, :limitCount';
                $parameters['limitStart'] = $limit[0];
                $parameters['limitCount'] = $limit[1];
            }

        }

        $statement = $this->execute($query, $parameters);

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }
 
}