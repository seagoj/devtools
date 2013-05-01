<?php
function _getRelPath($_runPath, $_libPath)
    {
        if ($_runPath==$_libPath) {
            return '';
        } else {
            $runPathArray = explode(DIRECTORY_SEPARATOR, $_runPath);
            $libPathArray = explode(DIRECTORY_SEPARATOR, $_libPath);
//            print "<div>RunPath: ".var_dump($runPathArray)."</div>";
//            print "<div>LibPath: ".var_dump($libPathArray)."</div>";
            $runPathDepth = sizeof($runPathArray);
            $libPathDepth = sizeof($libPathArray);
            $poppedFromRun = $poppedFromLib = $relPath = array();

            if ($runPathDepth==$libPathDepth) {
//                print "<div>Same Depth</div>";
                while ($runPathArray!=$libPathArray) {
                    array_push($poppedFromRun, array_pop($runPathArray));
                    array_push($poppedFromLib, array_pop($libPathArray));
                }
            } else {
                if ($runPathDepth>$libPathDepth) {
                    $longArray = 'run';
                    $shortArray = 'lib';
                } else {
                    $longArray = 'lib';
                    $shortArray = 'run';
                }

//                print $longArray."Path is deeper.";

                $longArrayDepth = sizeof(${$longArray.'PathArray'});
                $shortArrayDepth = sizeof(${$shortArray.'PathArray'});

                $i=0;

                while ($runPathArray[$i]==$libPathArray[$i] && $i<($runPathDepth-1) && $i<($libPathDepth-1)) {
                    $i++;
                }
                if($runPathArray[$i]!==$libPathArray[$i])
                    $i--;

//                print "<div>Same to index $i</div>";

                for ($i; $i<$longArrayDepth-1; $i++) {
                    if($i<$shortArrayDepth-1) {
                        array_push(${"poppedFrom".ucfirst($shortArray)}, array_pop(${$shortArray."PathArray"}));
                    }
                    array_push(${"poppedFrom".ucfirst($longArray)}, array_pop(${$longArray."PathArray"}));
                }
//                print "<div>PoppedFromRun: ".var_dump($poppedFromRun)."</div>";
//                print "<div>PoppedFromLib: ".var_dump($poppedFromLib)."</div>";
            }

            foreach ($poppedFromRun AS $pop) {
                array_push($relPath, "..");
            }

            foreach (array_reverse($poppedFromLib) AS $pop) {
                array_push($relPath, $pop);
            }
            return implode(DIRECTORY_SEPARATOR, $relPath).DIRECTORY_SEPARATOR;
        }
    }
    
$test = true;
$_runPath = '/home/travis/build/seagoj';
$_libPath = $_runPath.'/lib/lib';
$test = $test && assert(_getRelPath($_runPath, $_libPath)==='lib/lib/');

$_runPath = '/home/travis/build/seagoj';
$_libPath = $_runPath.'/lib';
$test = $test && assert(_getRelPath($_runPath, $_libPath)==='lib/');

$_runPath = '/home/travis/build/seagoj/tests';
$_libPath = '/home/travis/build/seagoj/lib';
$test = $test && assert(_getRelPath($_runPath, $_libPath)==='../lib/');

$_runPath = '/home/travis/build/seagoj';
$_libPath = $_runPath;
$test = $test && assert(_getRelPath($_runPath, $_libPath)==='');

$_runPath = '/home/travis/build/seagoj/tests/src';
$_libPath = '/home/travis/build/seagoj/lib';
$test = $test && assert(_getRelPath($_runPath, $_libPath)==='../../lib/');


if($test) print "Success";
    
