<?php
/**
 * API: API class to respond in various formats
 *
 * Formats $result data into various return types
 *
 * @name        API
 * @category    Seagoj
 * @package     Devtools
 * @author      Jeremy Seago <seagoj@gmail.com>
 * @version     1.0
 **/

namespace Devtools;

/**
 * API Class
 * Formats data into various return types
 *
 * @category    Seagoj
 * @package     Devtools
 * @author      Jeremy Seago <seagoj@gmail.com>
 * @version     1.0
 **/
class API
{
    /**
     * Options hash for the API class
     *
     * 'type'       Format of data to be returned
     * 'rowDelim'   Character that separates each row in the string format
     * 'colDelim'   Character that separates each value in the row in the string format
     **/
    private $options;

    /**
     * public API::__construct
     *
     * Loads the passed $options into the object
     *
     * @param   array       $options    Options to be used in the instance
     *
     * @return  API object
     **/
    public function __construct($options = array())
    {
        $this->options = \Devtools\API::loadOptions($options);
    }

    /**
     * public API::formatResponse
     *
     * Formats the data into specified response type
     *
     * @param   array   $result     Array of data to be formatted
     * @param   array   $options    Options array to be added to the instance
     *
     * @return  string  Formatted data string
     **/
    public static function formatResponse($result, $options=array())
    {
        $options = (!isset($this) || get_class($this) !== __CLASS__) ?
            API::loadOptions($options) :
            $this->options;
        switch($options['type']) {
            case 'json':
                /* header('Content-type: application/json'); */
                $resp = new \Devtools\Response;
                $resp->data($result);
                $response = $resp->json();
                break;
            case '':
            case 'string':
                $response = '';
                foreach ($result as $row) {
                    $rowStr = '';
                    foreach ($row as $value) {
                        $rowStr .= $rowStr==='' ?
                            $value :
                            ($options['colDelim'].$value);
                    }
                    $response .= ($rowStr.$options['rowDelim']);
                }
                break;
            default:
                throw new \InvalidArgumentException($options['type']." is not a valid return type.");
        }
        return $response;
    }

    /**
     * private API::loadOptions
     *
     * Loads options hash into the instance
     *
     * @param   array   $options    Options array to be added to the instance
     *
     * @return  array   Options array
     **/
    private static function loadOptions($options)
    {
        $defaults = array(
            'rowDelim' => "\n",
            'colDelim' => "|",
            'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : ''
        );
        return array_merge($defaults, $options);
    }
}
