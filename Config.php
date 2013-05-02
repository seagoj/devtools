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
    public $test;

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
        $class = explode('\\', get_class($obj));
        $class = $class[sizeof($class)-1];
        if (strpos($class, 'Test')===strlen($class)-4) {
            $class = substr($class, 0, strlen($class)-4);
        }
        $configFunc='_config'.ucfirst($class);

        if(method_exists($this, $configFunc))
            $this->config = $this->$configFunc();
        else
            $this->config = $this->_configDefault();
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
        $this->test = array(
            'Devtools\Dbg\msg'=>"<div class='err'><span class='errDesc'>#{0} in file #{4} on line #{5}</span></div>",
            'Devtools\Dbg\dump'=>"<div class='err'><span class='errDesc'>'#{0}' in file #{4} on line #{5}</span></div>",
            'Devtools\Dbg\test'=>true,
            'Devtools\Dbg\setNoCache'=>"<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>"
            );
        return isset($this->debug) && isset($this->test);
    }

    /**
     * Config::_configDefault
     *
     * Defines configuration for undefined classes
     *
     * @return bool
     */
    private function _configDefault()
    {
        $this->debug = false;
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

        // $this->conn = mysql_connect($info['host'].':'.$info['port'], $info['user'], $info['pass']);
        $this->conn = new mysqli($info['host'].':'.$info['port'], $info['user'], $info['pass'], 'demo');

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

    /**
     * Config::_configMarkdown()
     *
     * Defines configuration for the Markdown class
     *
     * @return bool
     */    
    private function _configMarkdown()
    {
        $this->debug = false;
        $this->test = array(
             'Devtools\Markdown\convert'=>"<h2>testLib</h2>\n",
            );
    }

    /**
     * Config::_configGit()
     *
     * Defines configuration for the Git class
     *
     * @return bool
     */    
    private function _configGit()
    {
        $this->debug = false;
        $this->test = array(
            'Devtools\Git\user'=>true,
            'Devtools\Git\host'=>true,
            'Devtools\Git\listRepos'=>array(
                "seagoj/bookmule",
                "seagoj/cookbook-apt",
                "seagoj/cookbook-bootstrap",
                "seagoj/cookbook-lib",
                "seagoj/cookbook-nginx",
                "seagoj/cookbook-php5-fpm",
                "seagoj/cookbook-redis",
                "seagoj/cookbook-ruby",
                "seagoj/cookbook-sass",
                "seagoj/devtools",
                "seagoj/dotfiles",
                "seagoj/jarvis",
                "seagoj/resume"
                )
            );
    }
}
