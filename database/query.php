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
 */

/**
 * The query object, an OOP alternative to written SQL statements
 *
 * @package     Riff
 * @subpackage  RiffDatabase
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 */

class RiffQuery
{

    /**
     * The GROUP BY clause, if any
     * 
     * @var string
     */
    private $groupBy;

    /**
     * The LIMIT clause, if any
     * 
     * @var string
     */
    private $limit;  

    /**
     * The ORDER BY clause, if any
     * 
     * @var string
     */
    private $orderBy; 

    /**
     * The plain text parameters
     * 
     * @var array
     */
    private $parameters;

    /**
     * The PDO parameters
     * 
     * @var array
     */ 
    private $PDOparameters;

    /**
     * The query
     * 
     * @var string
     */
    private $query;

    /**
     * The WHERE clause, if any
     * 
     * @var string
     */
    private $where;

    /**
     * Creates a new query
     * 
     * @param string $query
     * @param array[optional]   PDO bindValue() parameters
     * @param array[optional]   Plain text parameters (to be replaced manually)
     */
    public function __construct($query, $PDOparameters = array(), $parameters = array())
    {
        $this->query            = (string) $query;
        $this->parameters       = (array) $parameters;
        $this->PDOparameters    = (array) $PDOparameters;
        $this->groupBy          = '';
        $this->limit            = '';
        $this->orderBy          = '';
        $this->where            = '';
    }

    /**
     * Add grouping
     * 
     * @param string $groupBy   Column name to group by
     */
    public function addGroupBy($groupBy)
    {
        $this->groupBy = 'GROUP BY :groupBy';
        $this->addParameter('groupBy', (string) $groupBy);
    }

    /**
     * Add limit
     * 
     * @param int|array $limit
     */
    public function addLimit($limit)
    {
        $this->limit = 'LIMIT ';

        if (is_int($limit)) {
            $this->limit .= ':limit';
            $this->addPDOparameter('limit', $limit);
        } else if (is_array($limit) && count($limit) >= 2) {
            $this->limit .= ':limitStart, :limitCount';
            $this->addPDOParameters(array('limitStart' => (int) $limit[0], 'limitCount' => (int) $limit[1]));
        }
    }

    /**
     * Allows to order results
     * 
     * @param array|string orderBy  If array: [0] column name, [1] order method.
     *                              If string: column name, ASC assumed
     */ 
    public function addOrderBy($orderBy)
    {
        $allowedMethods = array('ASC', 'DESC');

        $this->orderBy = ' ORDER BY :orderBy';

        if (is_array($orderBy)) {

            if (count($orderBy) > 1 && in_array($orderBy[1], $allowedMethods)) {
                $this->orderBy .= ' :orderMethod';
                $this->addParameter('orderMethod', $orderBy[1]);
            }

            $this->addParameter('orderBy', (string) $orderBy[0]);

        } else if (is_string($orderBy)) {
            $this->addParameter('orderBy', $orderBy);
        } else {
            throw new RiffException('Order By is not allowed');
        }
    }

    /**
     * Add a parameter
     * 
     * @param string $key
     * @param string $value
     */
    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Add a group of parameters
     * 
     * @param array $parameters
     */
    public function addParameters($parameters)
    {
        if (!is_array($parameters)) throw new RiffException('Parameters is not an array');
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Add a PDO parameter
     * 
     * @param string $key
     * @param string $value
     */
    public function addPDOParameter($key, $value)
    {
        $this->PDOparameters[$key] = $value;
    }

    /**
     * Add a group of PDO parameters
     * 
     * @param array $parameters
     */
    public function addPDOParameters($parameters)
    {
        if (!is_array($parameters)) throw new RiffException('Parameters is not an array');

        $this->PDOparameters = array_merge($this->PDOparameters, $parameters);
    }

    /**
     * Add a WHERE clause to the query
     * 
     * @param array|string $where   If string: you can do anything that MySQL allows
     *                              If array:  key=>value
     */
    public function addWhere($where)
    {
        if (is_array($where)) {
            $this->where = 'WHERE ';

            foreach ($where as $key => $value) {
                $this->where .= RiffFilter::sanitize((string) $key) . ' = :' . (string) $key . ',';
            }

            $this->addPDOParameters($where);
            $this->where = rtrim($this->where, ','); 
        } else if (is_string($where)) {
            $this->where = RiffFilter::sanitize($where);
        } else {
            throw new RiffException('WHERE-clause is not valid');
        }
    }

    /**
     * Prepares the query for execution
     * 
     * @return string
     */ 
    public function fetchQuery()
    {
        $parts = array($this->query, $this->where, $this->groupBy, $this->orderBy, $this->limit);
        $fetchedQuery = implode(' ', $parts);

        // Manually sanitise and replace parameters that cannot be used by PDO
        // Use as many PDO parameters as possible!
        foreach ($this->parameters as $parameter => $value) {
            $value = RiffFilter::sanitize((string) $value);
            $fetchedQuery = str_replace(':' . $parameter, $value, $fetchedQuery);
        }

        return (string) $fetchedQuery;
    }

    /**
     * Get PDO parameters
     * 
     * @return array
     */ 
    public function getPDOParameters()
    {
        return $this->PDOparameters;
    }
}