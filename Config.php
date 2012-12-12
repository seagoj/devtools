<?php
/**
 * Config: Defines configuration for current project
 * 
 * @name      Config
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 */
namespace Devtools;
// require 'Autoload.php';
// Autoload::register();

/**
 * Config: Defines configuration for current project
 * 
 * @name      Config
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 */
class Config
{
    public $debug;
    public $conn;
    public $config;

    /**
     * public Config::__construct
     *
     * Constructor for Config class
     *
     * @param object $obj Object to return configuration for.
     *
     * @return void
     */
    public function __construct($obj)
    {
        //print get_class($obj);
        $class = explode('\\', get_class($obj));
        $class = $class[sizeof($class)-1];
        if (strpos($class, 'Test')===strlen($class)-4) {
            $class = substr($class, 0, strlen($class)-4);
        }
        $configFunc='_config'.ucfirst($class);
        if($class!='')
            $this->config = $this->$configFunc();
    }

    /**
     * Config::_configDbg
     *
     * Defines configuration for Dbg class
     *
     * @return bool
     */
    private function _configDbg()
    {
        $this->debug = true;
        return true;
    }

    /**
     * Config::_configModel()
     *
     * Defines configuration for Model class
     *
     * @return bool
     */
    private function _configModel()
    {
        $this->debug = false;
        $info = array(
                'db'=>'test-model',
                'user'=>'test-model',
                'pass'=>'YcfS9Q7TpSFxhECv',
                'host'=>'localhost',
                'port'=>''
        );
        $this->conn = mysql_connect($info['host'].':'.$info['port'], $info['user'], $info['pass']);
        mysql_select_db($info['db'], $this->conn);
        return true;
    }

    /**
     * Config::_configAutoload()
     *
     * Defines configuration for Autoload class()
     *
     * @return bool
     */    
    private function _configAutoload()
    {
        $this->debug = true;
        return true;
    }

    /**
     * Config::_configUnit()
     *
     * Defines configuration for the Unit class
     *
     * @return bool
     */    
    private function _configUnit()
    {
        $this->debug = true;
        return true;
    }

    /**
     * Config::_configConfig()
     *
     * Defines configuration for the Config class
     *
     * @return bool
     */    
    private function _configConfig()
    {
        $this->debug = true;
        return true;
    }

    /**
     * Config::_configRandData()
     *
     * Defines configuration for the RandData class
     *
     * @return bool
     */    
    private function _configRandData()
    {
        $this->debug = true;
        return true;
    }
}
