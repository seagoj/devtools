<?php namespace Devtools;
/**
 * Random data generator
 *
 * @category    Seagoj
 * @package     Devtools
 * @author      Jeremy Seago <seagoj@gmail.com>
 * @license     http://github.com/seagoj/Devtools/LICENSE MIT
 * @link        http://github.com/seagoj/Devtools
 **/

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
    private $dataTypes;

    /**
     * RandData::__construct
     *
     * Constructor for RandData class
     *
     * Populates valid data types into this.dataTypes
     **/
    public function __construct()
    {
        $this->dataTypes = array('String','Array','Integer','Bool','Double');
    }

    /**
     * RandData::get
     *
     * Returns random data
     *
     * Returns random data of type $type
     *
     * @param string $type Type of random data to be returned
     *
     * @returns     $type   Random data
     **/
    public function get($type)
    {
        $func = 'rand'.ucfirst(strtolower($type));

        if (in_array(ucfirst(strtolower($type)), $this->dataTypes)) {
            return $this->$func();
        } else {
            throw new \InvalidArgumentException("Data of type $type could not be generated.");
        }
    }

    /**
     * RandData::randArray
     *
     * Returns array of max size $max with random values
     *
     * @param integer $max max size of array
     *
     * @return array array of random values
     **/
    private function randArray($max = 100)
    {
        $array = array();
        $arrayLen = rand()%$max;

        for ($count = 0; $count<$arrayLen; $count++) {
            array_push($array, randData::randSign()*rand());
        }

        return $array;
    }

    /**
     * RandData::randInteger
     *
     * Returns random integer of max size $max
     *
     * @param integer $max max size of integer; defaults to PHP_INT_MAX
     *
     * @return integer integer of max size $max
     **/
    private function randInteger($max = PHP_INT_MAX)
    {
        return randData::randSign()*rand()%$max;
    }

    /**
     * RandData::randDouble
     *
     * Returns random double of max size $max
     *
     * @param integer $max max size of double; defaults to random
     *
     * @return double double of max size $max
     **/
    private function randDouble($max = 0)
    {
        if ($max == 0) {
            $max = mt_getrandmax();
        }

        return randData::randSign()*mt_rand() / $max * mt_rand();
    }

    /**
     * RandData::randSign
     *
     * Returns random sign based on random generated number
     *
     * @return int Returns either a 1 or -1 depending on random outcome
     **/
    private function randSign()
    {
        return pow(-1, rand(0, 1));
    }

    /**
     * RandData::randBool
     *
     * Returns random boolean based on random generated number
     *
     * @return boolean Returns true or false depending on random outcome
     **/
    private function randBool()
    {
        return (bool) rand(0, 1);
    }

    /**
     * RandData::randString
     *
     * Returns random string of max length $max
     *
     * @param integer $max Maximum length of string to return; defaults
     *                              to 100
     *
     * @return string Random string of max length $max
     **/
    private function randString($max = 100)
    {
        $stringLen = rand()%$max;
        $string = "";

        for ($i = 0; $i < $stringLen; $i++) {
            $string .= chr(rand()%255);
        }

        return $string;
    }
}
