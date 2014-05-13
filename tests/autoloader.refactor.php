<?php
$error = array(
    'type'  => 'file',
    'file'  => '/home/www/Error.log'
);

$debug = array(
    'type'  => 'file',
    'file'  => '/home/www/Debug.log'
);

if (!function_exists('findLibDir')) {
    function findLibDir($pathArray)
    {
        $libDirArray = array('vendor', 'lib');
        $found = false;

        // Traverse path to find path to libDir
        while (!$found) {
            $path = implode("/", $pathArray);
            foreach ($libDirArray as $dir) {
                if ( is_dir("$path/$dir") ) {
                    array_push($pathArray, $dir);
                    $found = true;
                    break;
                }
            }
            if (!$found) array_pop($pathArray);
        }
        return $pathArray;
    }
}

// Set initial values
$autoloadClass = "\Devtools\Autoload";

$cwd = explode('/', getcwd());
$script_path = explode('/', $_SERVER['SCRIPT_FILENAME']);

print "CWD: ".getcwd()."\n";
print "Script Path: ".$_SERVER['SCRIPT_FILENAME']."\n";

if ($cwd[1]!==$script_path[1]) {
    $relPath = $cwd;
    $rootPathArray = findLibDir($cwd);
    $libDir = array_pop($rootPathArray);
} else {
    while ($cwd[0] === $script_path[0]) {
            array_shift($cwd);
            array_shift($script_path);
    }
    var_dump($cwd);
    var_dump($script_path);

    /* $currentPathArray = explode( */
    /*  "/", */
    /*  dirname(getcwd()."/".$_SERVER['SCRIPT_FILENAME']) */
    /* ); */

    /* $currentPathDepth = count($currentPathArray); */
    /* if ($currentPathArray[$currentPathDepth-1] === 'bin' */
    /*  && $currentPathArray[$currentPathDepth-2] === 'vendor' */
    /* ) { */
    /*  array_pop($currentPathArray); */
    /*  array_pop($currentPathArray); */
    /*  $currentPathDepth -= 2; */
    /* } */
    /* $rootPathArray = findLibDir($currentPathArray); */
    /* $libDir = array_pop($rootPathArray); */
    /* $rootPathDepth = count($rootPathArray); */

    /* // Ignore the parts of the path that are the same for each */
    /* for ($i=0; $i<min($rootPathDepth, $currentPathDepth); $i++) { */
    /*  if ($currentPathArray[$i] !== $rootPathArray[$i]) { */
    /*      break; */
    /*  } */
    /* } */

    /* var_dump($currentPathArray); */
    /* var_dump($rootPathArray); */
    /* // Build the relative path from currentDir to libDir */
    /* $relPath = array(); */
    /* for ($j = $i; $j<$currentPathDepth; $j++) { */
    /*  array_push($relPath, ".."); */
    /* } */
    /* for ($i; $i<$rootPathDepth; $i++) { */
    /*  array_push($relPath, array_pop($rootPathArray[$i])); */
    /* } */
}

// Include file and register proper autoload function
if (!empty($relPath)) {
    $relPath = implode("/", $relPath)."/";
} else {
    $relPath = "";
}

$autoloadPath = $relPath.$libDir.str_replace('\\', '/', $autoloadClass).".php";
require_once $autoloadPath;
$autoloadClass::register();

if (isset($_REQUEST['debug'])) var_dump(spl_autoload_functions());

$errorLog = new \Devtools\Log($error);
$debugLog = new \Devtools\Log($debug);
