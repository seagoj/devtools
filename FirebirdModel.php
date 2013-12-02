<?php

namespace Devtools;

class FirebirdModel extends Model
{
    private $conn;

    public function __construct($options=array())
    {
        if(empty($options) && is_file('firebird-model.json')) {
            $options = (array) json_decode(file_get_contents('firebird-model.json'));
        }

        exit(var_export($options, true));

        $defaults = array(
            'host'          => "HOST",
            'location'      => "NJ",
            'environment'   => "TOMORROW",
            'dba'           => "DBA",
            'password'      => "PASSWORD",
            'type'          => 'firebird'
        );
        
        parent::__construct(array_merge($defaults, $options));
    }
/*
    public function query($query, $reduce=true, $debug=false)
    {
        if($debug) var_dump($query);
        if($debug) var_dump($this->conn);
        $q = ibase_query($this->conn, $query);
        if($debug) var_dump($q);
        $result = array();
        while ($r = ibase_fetch_assoc($q)) {
            if($debug) var_dump($r);
            array_push($result, $r);
        }

        ibase_free_result($q);
        if($debug) var_dump($result);
        return ($reduce ? $this->reduceResult($result) : $result);
    }
*/
    public function getLastPatients($limit=20)
    {
        return $this->query("select FIRST $limit * from PATIENT order by PATIENT_ID DESC");
    }    

    public function getPatientInfoByTelephoneRxID($id)
    {
        return $this->query("select FIRSTNAME, LASTNAME, BIRTHDATE, INSURANCE1ID, INSURANCE2ID, USUALDOCTORID, COPAYBRAND1, COPAYBRAND2, COPAYCOMPOUND1, COPAYCOMPOUND2, COPAYGENERIC1, COPAYGENERIC2, RX_RELEASE_ID, PHONE1, PHONE1EXT, PHONE2, PHONE2EXT, PHONEPREFERRED from PATIENT where PATIENT_ID = (select PATIENT_ID from TELEPHONERX where TELEPHONERX_ID = $id)");
    }

    public function getPatientNames($lastName)
    {
        $sql = "select FIRSTNAME, LASTNAME from PATIENT where LASTNAME like '".strtoupper($lastName)."%'";
        return $this->query($sql, false);
    }

    public function getInsuranceList($insuranceName)
    {
        $sql = "select NAME, INSURANCE_ID from INSURANCE where NAME like '%".strtoupper($insuranceName)."%' order by name";
        return $this->query($sql, false);
    }

    public function getDoctorList($doctorName)
    {
        $sql = "select LASTNAME, FIRSTNAME, DOCTOR_ID, FAX from DOCTOR where LASTNAME like '%".strtoupper($doctorName)."%'"; 
        return $this->query($sql, false);
    }

    public function getDrugList($drugName)
    {
        $sql = "select SPEEDCODE, NAME, FORMULA_ID from FORMULA where SPEEDCODE like '".strtoupper($drugName)."%'";
        return $this->query($sql, false);
    }

    public function validateInsurance($insurance_name)
    {
        $sql = "select INSURANCE_ID from INSURANCE where NAME='".$insurance_name."'";
        return $this->query($sql);
    }


    public function validateDrug($drugName)
    {
        $sql = "select FORMULA_ID from FORMULA where NAME='".$drugName."'";
        return $this->query($sql);
    }

    public function getInsuranceNameByID($id, $debug=false)
    {
        if( $id = $this->formatID($id) ) {
            $sql = "select NAME from INSURANCE where INSURANCE_ID=$id";
            $result = $this->query($sql);
            if( $result && $debug ) {
                return ($result ? $result : ibase_errmsg());
            } else {
                return $result;
            }
        } else {
            return "";
        }
    }

    public function getDrugNameByID($id, $debug=false)
    {
        if( $id = $this->formatID($id) ) {
            $sql = "select NAME from FORMULA where FORMULA_ID=$id";
            $result = $this->query($sql);
            if( $result && $debug ) {
                return ($result ? $result : ibase_errmsg());
            } else {
                return $result;
            }
        } else {
            return "";
        }
    }

    private function reduceResult($result)
    {
        if(is_array($result) && (count($result) == 1)) {
            reset($result);
            return $this->reduceResult($result[key($result)]);
        } else {
            return $result;
        }
    }

    private function formatID($id)
    {
        switch(gettype($id))
        {
            case 'string':
                $ret = intval($id);
                break;
            case 'NULL':
                $ret = false;
            default:
                $ret = $id;
        }

        return $ret;
    }
}
