<?php namespace Devtools;
/**
 * FirebirdModel
 *
 * Class for interacting with a Firebird database
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT: 1.0
 * @link     http://github.com/seagoj/Devtools/FirebirdModel.php
 **/

class FirebirdModel extends PDOModel
{
    const RET_VAL_STR = 'STRING';
    const RET_VAL_ARR = 'ARRAY';

    /**
     * set
     *
     * Set value of $key or $collection/$key
     *
     * @param String $set        Array of keys and values to set in $collection
     * @param String $collection Collection in which $key will be set to $value
     * @param Array  $where      Array describing where ckause
     *
     * @return boolean|null Status of assignment
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function set($assignments, $collection, $where=null)
    {
        $key = array_keys($assignments);
        $fields = array();
        foreach ($key as $name) {
            array_push($fields, ':'.$name);
        }
        $sql = "INSERT INTO $collection ("
            .self::stringify($key, true, '`')
        .") VALUES ("
            .implode(',', $fields)
        .")";

        /* @todo - update on duplicate */

        if (!is_null($where)) {
            extract(
                $this->buildWhere($where)
            );
            $sql .= $where;
        } else {
            $params = array();
        }

        return $this->query($sql, array_merge($assignments, $params), true);
    }

    /* @todo - Move Below to Entities/UseCases */

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

    public function getInsuranceNameByID($id)
    {
        $sql = "select NAME from INSURANCE where INSURANCE_ID=$%s";
        return $this->call($sql, $id);
    }

    public function getDrugNameByID($id)
    {
        $sql = "select NAME from FORMULA where FORMULA_ID=$%s";
        return $this->call($sql, $id);
    }

    public function getDoctorNameByID($id)
    {
        $sql = "select LASTNAME, FIRSTNAME, REGADDRESS1 from DOCTOR where DOCTOR_ID=%s";
        return $this->call($sql, $id);
    }

    public function getDoctorFaxByID($id)
    {
        $sql = "select FAX from DOCTOR where DOCTOR_ID=%s";
        return $this->call($sql, $id);
    }

    public function getSalesRepByDoctorID($id)
    {
        $sql = "select SALES_PERSON.LASTNAME, SALES_PERSON.FIRSTNAME from SALES_PERSON, DOCTOR where DOCTOR.SALES_PERSON_ID = SALES_PERSON.SALES_PERSON_ID and DOCTOR.DOCTOR_ID=%s";
        return $this->call($sql, $id, self::RET_VAL_ARR);
    }

    /**
     * @param string $sql
     */
    private function call($sql, $id, $nullIDRetValue=self::RET_VAL_STR)
    {
        $sql = $this->formatID($id) ? sprintf($sql, mysql_real_escape_string($id)) :  "";
        if (empty($sql)) {
            switch($nullIDRetValue) {
            case 'STRING':
                return '';
            case 'ARRAY':
                return array();
            }
        }
        $result = $this->query($sql);
        if ($result) {
            return ($result ? $result : ibase_errmsg());
        } else {
            return $result;
        }
    }

    /**
     * @return string
     */
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
