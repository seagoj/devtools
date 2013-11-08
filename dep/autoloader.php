<?php

$path = $_SERVER['SCRIPT_FILENAME'];
$currentPathArray = explode("/", $path);

while(!is_dir("$path/vendor") && !is_dir("$path/lib")) {
    $pathArray = explode("/", $path);
    $last = array_pop($pathArray);
    $path = implode("/", $pathArray);
}

$rootPath = "$path";
$rootPathArray = explode("/", $rootPath);

array_pop($currentPathArray);

if (file_exists("$rootPath/vendor")) {
    $flag = 'vendor';
} elseif (file_exists("$rootPath/lib")) {
    $flag = 'lib';
}

$i = 0;
while( $i<(min(count($rootPathArray), count($currentPathArray))) &&
    ($currentPathArray[$i] === $rootPathArray[$i])
)
{
    $i++;
}

$currentDiff = count($currentPathArray) - $i;
$rootDiff = count($rootPathArray) - $i;
$relPath = array();

for ($j = $i; $j<count($currentPathArray); $j++) {
    array_push($relPath, "..");
}

for ($i; $i<count($rootPathArray); $i++) {
    array_push($relPath, array_pop($rootPathArray[$i]));
}

include_once(implode("/", $relPath)."/$flag/Devtools/Autoload.php");

\Devtools\Autoload::register();
