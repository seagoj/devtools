<?php

namespace Devtools;

class FirebirdModel extends Model
{
    private $conn;

    public function __construct($options=array())
    {
        if(is_string($options) && is_file($options)) {
            $options = (array) json_decode(file_get_contents($options));
        } elseif(empty($options) && is_file('../../../../vendor/Devtools/firebird-model.json')) {
            $options = (array) json_decode(file_get_contents('../../../../vendor/Devtools/firebird-model.json'));
        }

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
        $sql = "select NAME, INSURANCE_ID from INSURANCE where ACTIVATED='T' and NAME like '%".strtoupper($insuranceName)."%' order by name";
        return $this->query($sql, false);
    }

    public function getDoctorList($doctorName)
    {
        $doctorName = mysql_real_escape_string($doctorName);
        $sql = "select LASTNAME, FIRSTNAME, DOCTOR_ID, FAX, REGADDRESS1, NAT_PROV_PROV_ID, DEA from DOCTOR where ACTIVATED='T' and LASTNAME like '".strtoupper($doctorName)."%'";
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
        $return = $this->query($sql);
        if (is_array($return)) {
            $return = array_pop($return);
            $return = $return['INSURANCE_ID'];
        }
        return $return;
    }

    public function validateDoctorName($doctor_name)
    {
        $lastName = substr($doctor_name, 0, $spacePos = strpos($doctor_name, ", "));
        $firstName = substr($doctor_name, $spacePos+2);
        $sql = "select DOCTOR_ID from DOCTOR where FIRSTNAME='".mysql_real_escape_string($firstName)."' and LASTNAME ='".mysql_real_escape_string($lastName)."'";
        $return = $this->query($sql);
        return $return;
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

    public function getDoctorNameByID($id, $debug=false)
    {
         if( $id = $this->formatID($id) ) {
            $sql = "select LASTNAME, FIRSTNAME, REGADDRESS1 from DOCTOR where DOCTOR_ID=$id";
            $result = $this->query($sql);
            if( $result && $debug ) {
                return ($result ? implode(', ', $result) : ibase_errmsg());
            } else {
                return implode(", ", $result);
            }
        } else {
            return "";
        }
    }

    public function getDoctorFaxByID($id, $debug=false)
    {
        if( $id = $this->formatID($id) ) {
            $sql = "select FAX from DOCTOR where DOCTOR_ID=$id";
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

    public function getSalesRepByDoctorID($id, $debug=false)
    {
        if ($doctor_id = $this->formatID($id)) {
            $sql = sprintf("select SALES_PERSON.LASTNAME, SALES_PERSON.FIRSTNAME from SALES_PERSON, DOCTOR where DOCTOR.SALES_PERSON_ID = SALES_PERSON.SALES_PERSON_ID and DOCTOR.DOCTOR_ID=%s",
                mysql_real_escape_string($doctor_id)
            );
            $result = $this->query($sql);
            if( $result && $debug ) {
                return ($result ? $result : ibase_errmsg());
            } else {
                return $result;
            }
        } else {
            return array();
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
                break;
            default:
                $ret = $id;
        }

        return $ret;
    }
}
