<?php namespace Devtools;

class RandData
{
    private $dataTypes;

    public function get($type)
    {
        $func = 'rand'.ucfirst(strtolower($type));

        if (in_array(ucfirst(strtolower($type)), $this->dataTypes)) {
            return $this->$func();
        } else {
            throw new \InvalidArgumentException("Data of type $type could not be generated.");
        }
    }

    public static function randArray($max = 100)
    {
        $array = array();
        $arrayLen = rand()%$max;

        for ($count = 0; $count<$arrayLen; $count++) {
            array_push($array, randData::randSign()*rand());
        }

        return $array;
    }

    public static function randInteger($max = PHP_INT_MAX)
    {
        return randData::randSign()*rand()%$max;
    }

    public static function randDouble($max = 0)
    {
        if ($max == 0) {
            $max = mt_getrandmax();
        }

        return randData::randSign()*mt_rand() / $max * mt_rand();
    }

    public static function randSign()
    {
        return pow(-1, rand(0, 1));
    }

    public static function randBool()
    {
        return (bool) rand(0, 1);
    }

    public static function randString($max = 100)
    {
        $stringLen = rand()%$max;
        $string = "";

        for ($i = 0; $i < $stringLen; $i++) {
            $string .= chr(rand()%255);
        }

        return $string;
    }
}
