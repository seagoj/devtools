<?php
namespace Devtools;

class RandData
{
    private $dataTypes;
    
    public function __construct() {
        $this->dataTypes = array('String','Array','Integer','Bool','Double','Null');
    }

    public function get($type) {
        $func = 'rand'.ucfirst(strtolower($type));
         
        if(in_array(ucfirst(strtolower($type)), $this->dataTypes)) {
            return $this->$func();
        }
        else
            die("Data of type $type could not be generated.");
    }
    
    private function randArray($max=100)
    {
        $array = array();
        $arrayLen = rand()%$max;

        for ($count=0;$count<$arrayLen;$count++) {
            array_push($array,randData::randSign()*rand());
        }

        return $array;
    }
    private function randInteger($max=PHP_INT_MAX)
    {
        return randData::randSign()*rand()%$max;
    }
    private function randDouble($max=0)
    {
        if($max == 0)
            $max = mt_getrandmax();

        return randData::randSign()*mt_rand() / $max * mt_rand();
    }
    private function randSign()
    {
        return pow(-1, rand(0,1));
    }
    private function randBool()
    {
        return (bool) rand(0,1);
    }
    private function randString($max=100)
    {
        $stringLen = rand()%$max;
        $string = "";

        for ($i = 0; $i < $stringLen; $i++) {
            $string .= chr(rand()%255);
        }

        return $string;
    }
    private function randNull() {
        return NULL;
    }
}
