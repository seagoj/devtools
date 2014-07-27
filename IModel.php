<?php
/**
 * ModelInterface
 *
 * Interface for Model class
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT: 1.0
 * @link     http://github.com/seagoj/Devtools/ModelInterface.php
 **/

namespace Devtools;

/**
 * Interface Model
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://github.com/seagoj/Devtools/ModelInterface.php
 */
interface IModel
{
    /**
     * get
     *
     * Returns value of passed parameter
     *
     * @param string $key name of parameter to retrieve value in model
     *
     * @return mixed Value of $key in model
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function get(string $key);

    /**
     * getAll
     *
     * Return all values in collection
     *
     * @param string $collectionName Collection to return all values
     *
     * @return array Array of values in collection
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function getAll(string $collectionName);

    /**
     * set
     *
     * Updates or inserts value of $key to $value in model
     *
     * @param string $key   Parameter to update in model
     * @param mixed  $value Value to store in $key in model
     *
     * @return boolean
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function set(string $key, $value);

    /**
     * query
     *
     * Return result of query against model
     *
     * @param string $queryString Query to run against model
     *
     * @return mixed Result of query against model
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function query(string $queryString);

    /**
     * sanitize
     *
     * Sanitizes $queryString and returns a sanitized string.
     *
     * @param string $queryString Query string to be sanitized.
     *
     * @return string Sanitized version of the queryString
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    function sanitize(string $queryString);
}
