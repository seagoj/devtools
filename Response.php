<?php namespace Devtools;
/**
 * Response
 *
 * Defines Response object for AJAX responses
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT:
 * @link     http://github.com/seagoj/Devtools/Response.php
 **/

/**
 * Response
 *
 * Defines Response object for AJAX responses
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://github.com/seagoj/Devtools/Response.php
 **/
class Response implements IService, \Serializable
{
    public $status;
    public $request;
    public $message;
    private $suppressHeader;
    private $data;
    private $publicProperties;
    private $model;

    /**
     * __construct
     *
     * Object initializer
     *
     * @param Model $model Model to pull data from in load()
     *
     * @return void
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __construct(\Devtools\Model $model = null)
    {
        $this->suppressHeader = $this->isSuppressHeader();
        $this->status = 'OK';
        $this->request = $this->getRequest();
        $this->message = $this->status ? "" : "Data could not be set\n";
        $this->publicProperties = getProperties($this);
        $this->model = $model;
    }

    /**
     * __get
     *
     * Class Getter
     *
     * @param string $property Name of value to return
     *
     * @return mixed Value of $this->$property
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __get($property)
    {
        return $this->data[$property];
    }

    /**
     * __set
     *
     * Class Setter
     *
     * @param string $property Name of property to set
     * @param mixed  $value    Value to set
     *
     * @return boolean Result of assignment
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __set($property, $value)
    {
        return $this->data[$property] = $value;
    }

    /**
     * __call
     *
     * Call undefined methods
     *
     * @param string $method Method being called
     * @param array  $params Parameters to call method wih
     *
     * @return mixed Return of called function.
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __call($method, $params)
    {
        switch($method) {
        case 'getSuppressHeaders':
        case 'getSuppressHeader':
            $this->isSuppressHeader($params);
            break;
        default:
            throw new \Exception("Method $method is not defined.");
            break;
        }
    }

    /**
     * __callStatic
     *
     * Call undefined methods
     *
     * @param string $method Method being called
     * @param array  $params Parameters to call method wih
     *
     * @return mixed Return of called function.
     * @author Jeremy Seago <seagoj@gmail.com>
    **/
    public static function __callStatic($method, $params)
    {
        switch($method) {
        case 'getSuppressHeaders':
        case 'getSuppressHeader':
            Response::isSuppressHeader($params);
            break;
        default:
            throw new \Exception("Method $method is not defined.");
            break;
        }
    }

    /**
     * __sleep
     *
     * Return array of property names to serialize
     *
     * @return array Properties to serialize
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __sleep()
    {
        return !is_null($this->data)
            ? array_merge($this->publicProperties, array_keys($this->data))
            : $this->publicProperties;
    }

    /**
     * message
     *
     * Add message to response
     *
     * @param string|array|object $msg   Value to be added as message
     * @param boolean             $error Error : message
     *
     * @return void
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function message($msg, $error=false)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }
        $this->message .= "$msg\n";
        if ($error) {
            $this->status = 'FAILED';
            trigger_error($msg);
        }
    }

    /**
     * fail
     *
     * Sets response status to failed and adds message
     *
     * @param string|array|object $msg Value to be added as message
     *
     * @return void
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function fail($msg)
    {
        $this->message($msg, true);
    }

    /**
     * ajax
     *
     * Performs AJAX call on page and returns results
     *
     * @param string  $url      URL of service
     * @param array   $request  Array of variables passed as request to service
     * @param boolean $dataOnly Return data of response only
     *
     * @return array Array representation of response
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function ajax($url='', $request=array(), $dataOnly=true)
    {
        if (!empty($url)) {
            $reset = false;
            $temp = array();
            if (!empty($request)) {
                $reset = true;
                $temp = isset($_REQUEST) ? $_REQUEST : array();
                $_REQUEST = array();
                foreach ($request as $key => $value) {
                    $_REQUEST[$key] = $value;
                }
            }
            $_REQUEST['suppress_header'] = true;
            ob_start();
            include $_SERVER['DOCUMENT_ROOT'].$url;
            $data = json_decode(ob_get_clean(), true);
            if ($dataOnly
                && in_array($data['status'], array('OK', 200))
            ) {
                unset(
                    $data['status'],
                    $data['message'],
                    $data['request'],
                    $data['suppress_header']
                );
            }
            if ($reset) {
                unset($temp['suppress_header']);
                $_REQUEST = $temp;
            }
            return $data;
        }
    }

    /**
     * load
     *
     * Loads result of SQL call into response object
     *
     * @param string $sql SQL query to return result
     *
     * @return array Array of results from SQL call
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function load($sql, $params = null)
    {
        if (is_null($this->model))  {
            throw new \Exception('No model available.');
        }

        return $this->data = $this->model->query($sql, $params, true);
/*         if ($q=mysql_query($sql) && !is_bool($q) && mysql_num_rows($q) > 0) { */
/*             /1* === SELECT === *1/ */
/*             $this->data = Model::reduceResult( */
/*                 Model::mysqlFetchAll($q) */
/*             ); */
/*         } else if ($q && is_bool($q) && mysql_num_rows($q) > 0 */
/*             && $insert_id = mysql_insert_id() */
/*         ) { */
/*             /1* === INSERT === *1/ */
/*             $this->data( */
/*                 array( */
/*                     'insert_id' => $insert_id */
/*                 ) */
/*             ); */
/*         } else if ($q && is_bool($q) && !$insert_id) { */
/*             /1* === UPDATE === *1/ */
/*             $this->data( */
/*                 array('update' => true) */
/*             ); */
/*         } else { */
/*             /1* === FAILURE === *1/ */
/*             $this->fail('ID not found.'); */
/*         } */
/*         return $this; */
    }

    /**
     * isSuppressHeader
     *
     * Checks request for suppress_header parameter
     *
     * @return bool Existence of suppress_header parameter
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function isSuppressHeader()
    {
        return array(
            'suppress_header' => (
                isset($_REQUEST['suppress_header'])
                    ? $_REQUEST['suppress_header']
                    : false
            )
        );
    }

    // =====INTERFACES=====
    /**
     * IService::data
     *
     * Add items to data for response
     *
     * @param array $data Array of keys and values to attach to response
     *
     * @return boolean Result of assignment
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function data(array $data)
    {
       if (empty($data)) {
           trigger_error(var_export(debug_backtrace(), true));
       }
       foreach ($data as $key => $value) {
           $this->$key = $value;
       }
       return $this;
    }

    /**
     * IService::getRequest
     *
     * Returns array based on _REQUEST
     *
     * @param array $validParams Array of valid parameters and their defaults
     *
     * @return array Values from request or fallback defaults
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function getRequest($validParams = null)
    {
        if (is_null($validParams)) {
            return (array)$_REQUEST;
        }
        $request = array();
        foreach ($validParams as $param => $default) {
            if (is_numeric($param)) {
                $param = $default;
                $default = null;
            }
            $request[$param] = isset($_REQUEST[$param])
                ? $_REQUEST[$param]
                : $default;
        }
        return $request;
    }

    /**
     * IService::delimited
     *
     * Returns delimited representation of response
     *
     * @param array $options Options array[
     *     'suppress_header' => false, // Supress writing headers
     *     'rowDelim'        => "\n",  // Row delim for delimitted output
     *     'colDelim'        => "|",   // Column delim for delimitted output
     * ]
     *
     * @return string Delimited representation of response
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function delimited($options = null)
    {
        $default = array(
            'rowDelim' => "\n",
            'colDelim' => "|"
        );

        if (!is_null($options)) {
            $options = array_merge(
                $default,
                $options
            );
        } else {
            $options = $default;
        }

       $response = '';
       foreach ($this->data as $row) {
           $rowStr = '';
           foreach ($row as $value) {
               $rowStr .= $rowStr==='' ?
                   $value :
                   ($options['colDelim'].$value);
           }
           $response .= ($rowStr.$options['rowDelim']);
       }
       return $response;
    }

    /**
     * IService::json
     *
     * Return response in json format
     *
     * @return string JSON representation of response
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function json()
    {
       if (!$this->isSuppressHeader() && !headers_sent()) {
           header('Content-type: application/json');
       }
       return json_encode($this->php());
    }

    /**
     * IService::php
     *
     * Return response as a PHP array
     *
     * @param boolean $serialize Return serialization : Return PHP
     *
     * @return array PHP array representation of response
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function php($serialize = false)
    {
       $ret = array();
       $sleep = $this->__sleep();
       var_dump($sleep);
       foreach ($sleep as $property) {
           $ret[$property] = $this->$property;
       }
       return $serialize ? serialize($ret) : $ret;
    }

    /**
     * Serializable::serialize
     *
     * Defines serialization of object
     *
     * @return string
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function serialize()
    {
        return $this->php(true);
    }

    /**
     * Serializable::unserialize
     *
     * Defines unserialization of object
     *
     * @param string $serialized Serialized representation of object
     *
     * @return object Object representation of serialized string
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $key => $value) {
            if (in_array($key, $this->publicProperties)) {
                $this->$key = $value;
            } else {
                $this->data[$key] = $value;
            }
        }
    }
}

/**
 * getProperties
 *
 * Returns accessible properties of $object
 *
 * @param Object $object Object to list accessible properties
 *
 * @return array Array of property names
 * @author Jeremy Seago <seagoj@gmail.com>
 **/
function getProperties($object)
{
    return array_keys(get_object_vars($object));
}
