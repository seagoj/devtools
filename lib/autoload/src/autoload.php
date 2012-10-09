<?php
/**
 * Lib.Autoload: Autoloader for PHP classes
 * This script searches the src and lib directories for classes and writes the relative path to a JSON file for that directory.
 * The JSON rebuilds each time a class is found and either returns the proper path or dies.
 *
 * @name		lib.autoload
 * @package		seago\devtools
 * @author 		Jeremy Seago <seagoj@gmail.com>
 * @copyright 	Copyright (c) 2012, Jeremy Seago
 * @license		http://opensource.org/licenses/mit-license.php
 * @version 	1.0
 * @link		https://github.com/seagoj/lib.autoload
 */
namespace seagoj\devtools;
//require_once('paths.php');

// Register custom autoload function for current namespace
spl_autoload_register(__NAMESPACE__."\\__autoload");

/**
 * __autoload Function
 * Magic function that is kicked off when an undefined object is created.
 * Instantiates class autoload to search for and include desired class definition.
 *
 * @access	public
 * @param	string	$class	Name of class to be instantiated
 * @return	void
 */

function __autoload($class)
{
	//dbg::dump(substr($class,strlen(__NAMESPACE__)+1));
    $load = new autoload(substr($class,strlen(__NAMESPACE__)+1));
    require_once($load->path);
}

/**
 * autoload Class
 * Searches for and registers path to class definitions found in source folders.
 *
 * @package		seago\devtools
 * @subpackage	autoload
 * @author		Jeremy Seago	<seagoj@gmail.com>
 * @version		1.0
 * @access		public
 * @public		$path			Set to path for requested class
 * @public		__construct(string,[array],[string])
 */
class autoload
{
    /**
     * @access	public
     * @var		string	$path	contains the value of the relative path to the class defintion
     */
    public $path;

    /**
     * @access	private
     * @var		array	$autoloadArray	Stores the array of discovered class definitions and paths as "class"=>"path"
     */
    private $autoloadArray;
    /**
     * @access	private
     * @var		array	$sourceLoc		Defines source directory locations
     */
    private $sourceLoc;
    /**
     * @access	private
     * @var		string	$includeFilename		Defines	path to include file; Set in buildIncludeFile()
     */
    private $includeFilename;
    /**
     * @access 	private
     * @var 	string		$template	Stores template for autoload.php; Set in buildTemplate()
     */
    private $template;
    /**
     * @access	private
     * @var		char	$this->tab			tab value for use in template; Set in __construct()
     */
    private $tab;
    /**
     * @access	private
     * @var 	string	$this->endline		endline value for use in template; Set in __construct()
     */
    private $endline;

    /**
     * autoload Constructor
     * If JSON exists, read paths from JSON and test for class definition
     * If JSON does not exist or path test fails, rebuild JSON by searching source directories for class definitions.
     * Return valid path or die if $class is not found
     *
     * @since	version 1.0
     * @access	public
     * @param  string $class     Name of class definition that we're searching for
     * @param  array  $sourceLoc Root source directories (relative to root) to search
     * @param  string $JSONLoc   Location of JSON file to read/write (relative to current directory)
     * @return void
     */
    public function __construct($class,$sourceLoc = array('src','lib','test','config'),$includeFilename='_paths.json')
    {
        $this->sourceLoc = $sourceLoc;
        $this->includeFilename = $includeFilename;
        $this->tab = "\t";
        $this->endline = "\r\n";
        $this->autoloadArray = array();
        
        if (!isset($this->autoloadArray)) {
            if (file_exists($this->includeFilename)) {
                $this->buildArrayFromFile();
                if(!array_key_exists($class,$this->autoloadArray)) {
                	$this->buildFileFromSearch();
                }
            } else {
            	$this->buildFileFromSearch();
            }
        }

        if (!array_key_exists($class,$this->autoloadArray)) {
            $this->buildFileFromSearch();
            if (array_key_exists($class,$this->autoloadArray)) {
            	//dbg::dump($this->autoloadArray);
                return $this->path = $this->autoloadArray[$class];
            } else {
                die("$class not found in source.");
            }
        } else {
            return $this->path = $this->autoloadArray[$class];
        }
    }
    
    private function __constructOLD($class,$sourceLoc = array('src','lib','test'),$includeFilename='paths.php')
    {
    	$this->sourceLoc = $sourceLoc;
    	$this->includeFilename = $includeFilename;
    	$this->tab = "\t";
    	$this->endline = "\r\n";
    
    	if (!isset($this->autoloadArray)) {
    		if (file_exists($this->includeFilename)) {
    			$this->getRequire();
    			if(!array_key_exists($class,$this->autoloadArray)) {
    				$this->buildIncludeFile();
    			}
    		} else {
    			$this->buildIncludeFile();
    		}
    	}
    
    	if (!array_key_exists($class,$this->autoloadArray)) {
    		$this->buildIncludeFile();
    		if (array_key_exists($class,$this->autoloadArray)) {
    			return $this->path = $this->autoloadArray[$class];
    		} else {
    			die("$class not found in source.");
    		}
    	} else {
    		return $this->path = $this->autoloadArray[$class];
    	}
    }
        
    /**
     * cacheJSON Function
     * Determines current script directory
     * Starts search for classes in each source directory
     * Saves resulting array of 'source folder'=>'relative path from current folder to source' to JSON file
     *
     * @since	version 1.0
     * @access	private
     * @return void
     */
    private function buildIncludeFile()
    {
        $fileArray= explode("/",$_SERVER['SCRIPT_FILENAME']);
        array_pop($fileArray);
        $runPath = implode("/",$fileArray);
        $searchArray = $this->getSourcePath();

        foreach ($searchArray as $searchLoc) {
            $this->findClasses($searchLoc,$runPath);
        }
        $this->buildTemplate();
        
        $require = '';
        foreach($this->autoloadArray AS $class=>$path) {
        	$require .= 'require_once("'.$path.'");'.$this->endline;
        }

        file_put_contents($this->includeFilename,$this->template.$require);
    }
    /**
     * findClasses Function
     * Searches source locations for class definitions
     * Adds classes found to autoload.autoloadArray[] in form of $class=>'Relative path to class'
     *
     * @since	version 1.0
     * @access	private
     * @param  array  $searchLoc Array of source locations relative to root
     * @param  string $runPath   Path to current directory
     * @return void
     */
    private function findClasses($searchLoc,$runPath)
    {
        $dir = opendir($searchLoc);
        while (false !== ($entry = readdir($dir))) {
            if ($entry=='.' || $entry=='..') {
            } elseif (is_dir($searchLoc."/".$entry)) {
                $this->findClasses($searchLoc."/".$entry,$runPath);
            } else {
                if (substr($entry,strlen($entry)-4)==".php") {
                    $code = file_get_contents($searchLoc.'/'.$entry);
                    if (preg_match_all("@class [a-zA-Z0-9_]*\s(extends\s[a-zA-Z0-9]*)?\s*{@",$code,$classes)) {
                        foreach ($classes as $file) {
                        	$instance = $file[0];
                        	/*
                             *  remove class prefix and \r\n{ postfix to get class name
                             */
                            $prefix = "class ";
                            $prefixLen = strlen($prefix);
                            if(strpos($instance,$prefix)==0 && strpos($instance,$prefix)!==false) {
                              	$classNoPrefix = substr($instance,$prefixLen);
                               	preg_match_all('/^[a-zA-Z0-9_]*/',$classNoPrefix,$classArray);
                               	foreach ($classArray[0] as $class) {
                                   	if ($class!='') {
	                                   	$this->autoloadArray[$class]=$this->getRelativePath($runPath,$searchLoc).$class.".php";
                                   	}
                               	}
                        	}
                        }
                    }
                }
            }
        }
    }
    /**
     * getSourcePath Function
     * Returns array of paths to directories in autoload.sourceLoc
     *
     * @since	version 1.0
     * @access	private
     * @return array 'source directory name'=>'relative path to directory'
     */
    private function getSourcePath()
    {
    	//dbg::dump($_SERVER['SCRIPT_FILENAME']);
        $array = explode("/",$_SERVER['SCRIPT_FILENAME']);
        $filename = array_pop($array);
        $popped = array_pop($array);
        $return = array();
        //dbg::dump($array,false);
        while (!in_array($popped,$this->sourceLoc)||sizeof($array)==0) {
            $popped = array_pop($array);
        }
        //dbg::dump($popped,false);
        foreach ($this->sourceLoc AS $loc) {
            $var = $loc."Path";
            $$var =implode("/",$array)."/$loc";
            $return = array_merge($return,array($loc=>$$var));
        }
		//dbg::dump($return,false);
        return $return;
    }
    /**
     * getRelativePath Function
     * Returns a path to the second directory relative to the first
     *
     * @param  string $run
     * @param  string $search
     * @return string Path to $search relative to $run
     */
    private function getRelativePath($run, $search)
    {
    	//dbg::dump("run: ".$run."; search: ".$search,false);
        if ($run==$search) {
            return '';
        } else {
            $runArray = explode("/",$run);
            $searchArray = explode("/",$search);
            $poppedFromRun = array();
            $poppedFromSearch = array();
            $relPath = array();
            /*
            $count = min(array(sizeof($runArray),sizeof($searchArray)));
            dbg::dump($count);
            */
            if(sizeof($runArray)==sizeof($searchArray)) {
            	while($runArray!=$searchArray) {
            		array_push($poppedFromRun,array_pop($runArray));
            		array_push($poppedFromSearch,array_pop($searchArray));
            	}
            } else if(sizeof($runArray)>sizeof($searchArray)) {
            	$i=0;
            	while($runArray[$i]==$searchArray[$i]) {
            		$i++;
            	}
            	for($i+1;$i<sizeof($searchArray);$i++) {
            		array_push($poppedFromRun,array_pop($runArray));
            		array_push($poppedFromSearch,array_pop($searchArray));
            	}
            	for($i+1;$i<sizeof($runArray); $i++) {
            		array_push($poppedFromRun,array_pop($runArray));
            	}
            } else if(sizeof($runArray)<sizeof($searchArray)) {
            	$i=0;
            	while($runArray[$i]==$searchArray[$i]) {
            		$i++;
            	}
            	//dbg::dump($runArray[$i]);
            	for($i;$i<sizeof($runArray)+1;$i++) {
            		array_push($poppedFromRun,array_pop($runArray));
            		array_push($poppedFromSearch,array_pop($searchArray));
            		//dbg::dump($searchArray,false);
            	}
            	for($i;$i<=sizeof($searchArray)+1; $i++) {
            		array_push($poppedFromSearch,array_pop($searchArray));
            		//dbg::dump($searchArray,false);
            	}
            	//dbg::dump($poppedFromSearch,false);
            	//dbg::dump($poppedFromRun,false);
            }
            
            
            //dbg::dump($runArray,false);
            //dbg::dump($searchArray,false);
            //dbg::dump($poppedFromRun,false);
            //dbg::msg("Popped from search: ");
            //dbg::dump($poppedFromSearch,false);
            //dbg::dump(array_pop($poppedFromSearch),false);
            
            foreach($poppedFromRun AS $pop) {
            	array_push($relPath,"..");
            }
            
            foreach(array_reverse($poppedFromSearch) AS $pop) {
            	array_push($relPath,$pop);
            	//	dbg::dump($relPath,false);
            }
            //$relPath = array_reverse($relPath);
            //$relPath = array_merge($relPath,$poppedFromSearch);
            
            //dbg::dump($relPath,false);
            
            
            /*
             * $runArray = explode("/",$run);
            $searchArray = explode("/",$search);
            
            sizeof($runArray)>=sizeof($searchArray) ? $count = sizeof($searchArray) : $count = sizeof($runArray);
            for ($i=0;$i<$count;$i++) {
                if($runArray[$i]!=$searchArray[$i])
                    $delta = $i;
            }

            if (sizeof($runArray)>=sizeof($searchArray)) {
                $relPath = array('..');
                for($i=sizeof($searchArray)-1;$i<=sizeof($runArray)-1;$i++)
                    array_push($relPath,$runArray[$i]);
            } else {
            $relPath = array('..');
            for ($i=sizeof($runArray)-1;$i<=sizeof($searchArray)-1;$i++) {
            array_push($relPath,$searchArray[$i]);
            }
            }
			*/
            return implode("/",$relPath)."/";
        }
    }
	private function getRequire() {
		$code = file_get_contents($this->includeFilename);
		
		$this->buildTemplate();
		
		$requires = substr($code,strlen($this->template));
		preg_match_all("/\"[a-zA-Z0-9.\/]*\"/",$requires,$matches);
		foreach($matches AS $file) {
			foreach($file AS $path) {
				$this->autoloadArray[substr($path,strrpos($path,'/')+1,strrpos($path,'.')-strrpos($path,'/')-1)] = $path;
			}
		}
		//dbg::dump($this->autoloadArray);
	}
    private function buildTemplate() {
    	if(!isset($this->autoloadArray))
    		$this->buildIncludeFile();
    	
    	$pathToAutoload = $this->autoloadArray['autoload'];
    	$this->endline = "\r\n";
    	$this->tab = "\t";
    	
    	$this->template = '<?php'.$this->endline
    	.'/**'.$this->endline
    	.' * DO NOT EDIT: PAGE GENERATED BY CLASS.AUTOLOAD'.$this->endline
    	.' */'.$this->endline
    	.'namespace '.__NAMESPACE__.';'.$this->endline.$this->endline
    	.'// Register custom autoload function for current namespace'.$this->endline
    	.'spl_autoload_register(__NAMESPACE__."\\__autoload");'.$this->endline.$this->endline
    	.'/**'.$this->endline
    	.' * __autoload Function'.$this->endline
    	.' * Magic function that is kicked off when an undefined object is created.'.$this->endline
    	.' * Instantiates class autoload to search for and include desired class definition.'.$this->endline
    	.' *'.$this->endline
    	.' * @access	public'.$this->endline
    	.' * @param	string	$class	Name of class to be instantiated'.$this->endline
    	.' * @return	void'.$this->endline
    	.' */'.$this->endline
    	.'function __autoload($class) {'.$this->endline
    	.$this->tab.'require_once("'.$pathToAutoload.'");'.$this->endline
    	.$this->tab.'$load = new autoload(substr($class,strlen(__NAMESPACE__)+1));'.$this->endline
    	.$this->tab.'require_once($load->path);'.$this->endline.'}'.$this->endline.$this->endline;
    }
	private function buildArrayFromFile() {
		$this->autoloadArray = json_decode(file_get_contents($this->includeFilename));
		dbg::dump(__METHOD__." is a work in progress",false);
	}
	private function buildFileFromSearch() {
		$fileArray= explode("/",$_SERVER['SCRIPT_FILENAME']);
		array_pop($fileArray);
		$runPath = implode("/",$fileArray);
		$searchArray = $this->getSourcePath();
		
		foreach ($searchArray as $searchLoc) {
			$this->findClasses($searchLoc,$runPath);
		}
		/*
		$this->buildTemplate();
		
		$require = '';
		foreach($this->autoloadArray AS $class=>$path) {
			$require .= 'require_once("'.$path.'");'.$this->endline;
		}
		*/
		
		//dbg::dump(json_encode($this->autoloadArray));
		file_put_contents($this->includeFilename,json_encode($this->autoloadArray));
		//dbg::dump(__METHOD__." is a work in progress",false);
	}
}
