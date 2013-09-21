<?php
/**
 * Autoload: Autoloader for PHP classes
 *
 * Autoloads instantiated classes across multiple namespaces using PSR-0 standard
 *
 * @name      Autoload
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 */
namespace Devtools;

/**
 * autoload Class
 * Searches for and registers path to class definitions found in source folders.
 *
 * @category   Devtools
 * @package    Seagoj
 * @subpackage Autoload
 * @author     Jeremy Seago    <seagoj@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php, MIT
 * @version    1.0
 * @link       https://github.com/seagoj/devtools
 */
class Autoload
{
    /**
     * Path to library/include files
     *
     * Contains the path to the library/include files defined by the class
     **/
    private $libPath;

    /**
     * Run path of the script
     *
     * Contains the current working directory of the project
     **/
    private $runPath;

    /**
     * public Autoload::__construct
     *
     * Sets $libPath and $runPath
     *
     * @param string $currentDir Path to Dir where this script runs
     *
     * @return void
     */
    public function __construct($currentDir = __DIR__)
    {
        switch (self::checkEnv()) {
            case 'PHPUNIT':
                $this->runPath = $currentDir;
                $this->libPath = $this->runPath.'/lib';
                break;
                // @codeCoverageIgnoreStart
            default:
                $this->runPath = $this->getPath($_SERVER['SCRIPT_FILENAME']);
                $this->libPath = $this->getPath($currentDir);
                break;
                // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Autoload::checkEnv
     *
     * Returns current running environment
     *
     * Determines if script is being run in production or a testing/development
     * environment
     *
     * @Return  string  Descriptor of environment
     **/
    public function checkEnv()
    {
        $path = explode('/', $_SERVER['SCRIPT_FILENAME']);
        
        return in_array('phpunit', $path) ? 'PHPUNIT' : '';
    }

    /**
     * public Autoload.Register()
     *
     * Registers the function to use as the autoloader
     *
     * @param bool $prepend Prepend value to pass on to spl_autoload_register
     *
     * @return void
     */
    public static function register($options=array())
    {
        $defaults = array(
            "prepend"=> false,
        );

        $options = array_merge($defaults, $options);

        spl_autoload_register(array(new self, 'autoload'), true, $options['prepend']);
    }

    /**
     * Autoload.autoload()
     *
     * @param string $class Class that the autoloader is searching for
     *
     * @return void
     */
    private function autoload($class)
    {
        $file = $this->getRelPath().implode(DIRECTORY_SEPARATOR, explode('\\', $class)).'.php';
        return include $file;
    }

    /**
     * Private Static Autoload::getPath
     *
     * Returns Path to $file
     *
     * @param string $file File to return the path of
     *
     * @return string Path to $file
     */
    private function getPath($file)
    {
        return substr($file, 0, strripos($file, DIRECTORY_SEPARATOR));
    }

    /**
     * Private Autoload::getRelPath
     *
     * Returns Path to $libPath relative to $runPath
     *
     * @param string $runPath Path to be used as home directory
     * @param string $libPath Path to find relative path to
     *
     * @return string Path to $file
     */
    private function getRelPath($runPath = null, $libPath = null)
    {
        $runPath = is_null($runPath) ? $this->runPath : $runPath;
        $libPath = is_null($libPath) ? $this->libPath : $libPath;

        if ($runPath==$libPath) {
            return '';
        } else {
            $runPathArray = explode(DIRECTORY_SEPARATOR, $runPath);
            $libPathArray = explode(DIRECTORY_SEPARATOR, $libPath);
            $runPathDepth = sizeof($runPathArray);
            $libPathDepth = sizeof($libPathArray);
            $poppedFromRun = $poppedFromLib = $relPath = array();

            // @codeCoverageIgnoreStart
            if ($runPathDepth==$libPathDepth && self::checkEnv()!=='PHPUNIT') {
                while ($runPathArray!=$libPathArray) {
                    array_push($poppedFromRun, array_pop($runPathArray));
                    array_push($poppedFromLib, array_pop($libPathArray));
                }
            } else {
            // @codeCoverageIgnoreEnd
                if ($runPathDepth>$libPathDepth) {
                    $longArray = 'run';
                    $shortArray = 'lib';
                } else {
                    $longArray = 'lib';
                    $shortArray = 'run';
                }

                $longArrayDepth = sizeof(${$longArray.'PathArray'});
                $shortArrayDepth = sizeof(${$shortArray.'PathArray'});

                $i=0;

                while ($runPathArray[$i]==$libPathArray[$i] && $i<($runPathDepth-1) && $i<($libPathDepth-1)) {
                    $i++;
                }
                if ($runPathArray[$i]!==$libPathArray[$i]) {
                    $i--;
                }

                for ($i; $i<$longArrayDepth-1; $i++) {
                    if ($i<$shortArrayDepth-1) {
                        array_push(${"poppedFrom".ucfirst($shortArray)}, array_pop(${$shortArray."PathArray"}));
                    }
                    array_push(${"poppedFrom".ucfirst($longArray)}, array_pop(${$longArray."PathArray"}));
                }             
            }

            foreach ($poppedFromRun as $pop) {
                array_push($relPath, "..");
            }

            foreach (array_reverse($poppedFromLib) as $pop) {
                array_push($relPath, $pop);
            }

            return implode(DIRECTORY_SEPARATOR, $relPath).DIRECTORY_SEPARATOR;
        }
    }
}
