<?php
/**
 * Random data generator
 *
 * @category    Seagoj
 * @package     Devtools
 * @author      Jeremy Seago <seagoj@gmail.com>
 * @license     http://github.com/seagoj/Devtools/LICENSE MIT
 * @link        http://github.com/seagoj/Devtools
 **/

namespace Devtools;

/**
 * Class RandData
 *
 * @category    Seagoj
 * @package     Devtools
 * @author      Jeremy Seago <seagoj@gmail.com>
 * @license     http://github.com/seagoj/Devtools/LICENSE MIT
 * @link        http://github.com/seagoj/Devtools
 *
 * Returns random values of the passed type
 **/
class RandData
{
    /**
     * Array of valid data types
     *
     * Passed types are validataed against values in this array.
     **/
    private $_dataTypes;

    /**
     * RandData::__construct
     *
     * Constructor for RandData class
     * 
     * Populates valid data types into this._dataTypes
     **/
    public function __construct()
    {
        $this->_dataTypes = array('String','Array','Integer','Bool','Double');
    }

    public function get($type)
    {
        $func = '_rand'.ucfirst(strtolower($type));

        if (in_array(ucfirst(strtolower($type)), $this->_dataTypes)) {
            return $this->$func();
        } else
            die("Data of type $type could not be generated.");
    }

    private function _randArray($max=100)
    {
        $array = array();
        $arrayLen = rand()%$max;

        for ($count=0;$count<$arrayLen;$count++) {
            array_push($array,randData::randSign()*rand());
        }

        return $array;
    }
    private function _randInteger($max=PHP_INT_MAX)
    {
        return randData::_randSign()*rand()%$max;
    }
    private function _randDouble($max=0)
    {
        if($max == 0)
            $max = mt_getrandmax();

        return randData::_randSign()*mt_rand() / $max * mt_rand();
    }
    private function _randSign()
    {
        return pow(-1, rand(0,1));
    }
    private function _randBool()
    {
        return (bool) rand(0,1);
    }
    private function _randString($max=100)
    {
        $stringLen = rand()%$max;
        $string = "";

        for ($i = 0; $i < $stringLen; $i++) {
            $string .= chr(rand()%255);
        }

        return $string;
    }
}
