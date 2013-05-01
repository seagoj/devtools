<?php
function _getRelPath($_runPath, $_libPath)
    {
        if ($_runPath==$_libPath) {
            return '';
        } else {
            $runPathArray = explode(DIRECTORY_SEPARATOR, $_runPath);
            $libPathArray = explode(DIRECTORY_SEPARATOR, $_libPath);
            $runPathDepth = sizeof($runPathArray);
            $libPathDepth = sizeof($libPathArray);
            $poppedFromRun = $poppedFromLib = $relPath = array();

            if ($runPathDepth==$libPathDepth) {
                while ($runPathArray!=$libPathArray) {
                    array_push($poppedFromRun, array_pop($runPathArray));
                    array_push($poppedFromLib, array_pop($libPathArray));
                }
            } else {
                $i=0;

                while ($runPathArray[$i]==$libPathArray[$i] && $i<($runPathDepth-1)) {
                    $i++;
                }

                if ($runPathDepth>$libPathDepth) {
                    $longArray = 'run';
                    $shortArray = 'lib';
                } else {
                    $longArray = 'lib';
                    $shortArray = 'run';
                }

                $longArrayDepth = sizeof(${$longArray.'PathArray'});
                $shortArrayDepth = sizeof(${$shortArray.'PathArray'});

                for ($i; $i<$shortArrayDepth; $i++) {
                    array_push($poppedFromRun, array_pop($runPathArray));
                    array_push($poppedFromLib, array_pop($libPathArray));
                }

                for ($j=$shortArrayDepth; $j<$longArrayDepth; $j++) {
                    array_push(${"poppedFrom".ucfirst($longArray)}, array_pop(${$longArray."PathArray"}));
                }
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

    
