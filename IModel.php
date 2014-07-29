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
     * @param String $key        Name of parameter to retrieve value in model
     * @param String $collection Optional collection name
     *
     * @return Mixed Value of $key in model
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function get(\String $key, \String $collection);

    /**
     * getAll
     *
     * Return all values in collection
     *
     * @param String $collection Collection to return all values
     *
     * @return Array Array of values in collection
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function getAll(\String $collection);

    /**
     * set
     *
     * Updates or inserts value of $key to $value in model
     *
     * @param String $key        Parameter to update in model
     * @param Mixed  $value      Value to store in $key in model
     * @param String $collection Optional collection name
     *
     * @return Boolean
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function set(\String $key, $value, \String $collection);

    /**
     * query
     *
     * Return result of query against model
     *
     * @param String $queryString Query to run against model
     *
     * @return Mixed Result of query against model
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function query(\String $queryString);

    /**
     * sanitize
     *
     * Sanitizes $queryString and returns a sanitized string.
     *
     * @param String $queryString Query string to be sanitized.
     *
     * @return String Sanitized version of the queryString
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function sanitize(\String $queryString);
}
