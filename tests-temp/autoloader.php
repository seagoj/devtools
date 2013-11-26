<?php
$error = array(
    'type'  => 'file',
    'file'  => '/home/www/Error.log'
);

$debug = array(
    'type'  => 'file',
    'file'  => '/home/www/Debug.log'
);

function findLibDir($pathArray)
{
    $libDirArray = array('vendor', 'lib');
    $found = false;
    
    // Traverse path to find path to libDir
    while(!$found) {
        $path = implode("/", $pathArray);
        foreach ($libDirArray as $dir) {
            if( is_dir("$path/$dir") ) {
                array_push($pathArray, $dir); 
                $found = true;
                break;
            }
        }
        if (!$found) array_pop($pathArray);
    }
    return $pathArray;
}

// Set initial values
$autoloadClass = "\Devtools\Autoload";
$currentPathArray = explode("/", dirname(getcwd()."/".$_SERVER['SCRIPT_FILENAME']));
$currentPathDepth = count($currentPathArray);
if($currentPathArray[$currentPathDepth-1] === 'bin' &&
    $currentPathArray[$currentPathDepth-2] === 'vendor' )
{
    array_pop($currentPathArray);
    array_pop($currentPathArray);
    $currentPathDepth -= 2;
}
$rootPathArray = findLibDir($currentPathArray);
$libDir = array_pop($rootPathArray);
$rootPathDepth = count($rootPathArray);

// Ignore the parts of the path that are the same for each
for ($i=0; $i<min($rootPathDepth, $currentPathDepth); $i++)
{
    if ($currentPathArray[$i] !== $rootPathArray[$i]) break;
}

// Build the relative path from currentDir to libDir
$relPath = array();
for ($j = $i; $j<$currentPathDepth; $j++) {
    array_push($relPath, "..");
}
for ($i; $i<$rootPathDepth; $i++) {
    array_push($relPath, array_pop($rootPathArray[$i]));
}

// Include file and register proper autoload function
if(!empty($relPath)) {
    $relPath = implode("/", $relPath)."/";
} else {
    $relPath = "";
}
$autoloadPath = $relPath.$libDir.str_replace('\\', '/', $autoloadClass).".php";
var_dump($autoloadPath);
include_once $autoloadPath;
$autoloadClass::register();

if (isset($_REQUEST['debug'])) var_dump(spl_autoload_functions());

var_dump("Creating Logs");
$errorLog = new \Devtools\Log($error);
$debugLog = new \Devtools\Log($debug);
var_dump("Logs created");
