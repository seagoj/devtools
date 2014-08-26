<?php
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

namespace Devtools;

/**
 * Class FirebirdModel
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://github.com/seagoj/Devtools/FirebirdModel.php
 */
class FirebirdModel extends Model
{
    const RET_VAL_STR = 'STRING';
    const RET_VAL_ARR = 'ARRAY';

    private $options;

    /**
     * __construct
     *
     * Constructor for FirebirdModel
     *
     * @param Mixed $connection Firebird resource or path to options json
     *
     * @return FirebirdModel Model object for Firebird
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __construct($connection = null)
    {
        if (is_resource($connection) || $connection==='TestResource') {
            $this->connection = $connection;
        } else {
            if (is_string($connection) && is_file($connection)) {
                $connection = (array) json_decode(file_get_contents($connection));
            } else {
                $connection = array();
            }
            $defaults = array(
                'host'          => "HOST",
                'location'      => "NJ",
                'environment'   => "TOMORROW",
                'dba'           => "DBA",
                'password'      => "PASSWORD",
                'type'          => 'firebird'
            );
            $this->options = array_merge($defaults, $connection);
            $this->connect();
        }
        if (!$this->connection) {
            throw new \Exception('connection to host could not be established');
        }
    }

    public function connect()
    {
        if (!$this->connection = \ibase_pconnect(
            sprintf(
                '%s:C:\\%s\\%s\\CMPDWIN.PKF',
                $this->options['host'],
                $this->options['environment'],
                $this->options['location']
            ),
            $this->options['dba'],
            $this->options['password']
        )) {
            throw new \Exception('connection to host could not be established');
        }
        return $this->connected = isset($this->connection);
    }


    /**
     * query
     *
     * Query firebird model
     *
     * @param String  $sql    Query string
     * @param Boolean $reduce Reduce result if true
     *
     * @return mixed Result of query
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function query($sql, $reduce=false)
    {
        $sql = \Devtools\FirebirdModel::sanitize($sql);
        /* if (gettype($this->connection) === 'resource') { */
            $q = ibase_query($this->connection, $sql);
            if (!(is_bool($q) || is_int($q))) {
                $result = array();
                while ($row = ibase_fetch_assoc($q, IBASE_TEXT)) {
                    array_push($result, $row);
                }
                ibase_free_result($q);
            } else {
                $result = $q;
            }
            return $reduce ? $this->reduceResult($result) : $result;
        /* } else { */
        /*     throw new \InvalidArgumentException('Invalid connection type.'); */
        /* } */
    }

    /**
     * get
     *
     * Return value based on $key
     *
     * @param String $key        Parameter whose value is returned
     * @param String $collection Collection to search for $key
     * @param Array  $where      Array of where clause elements
     *
     * @return Mixed Value of $key
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function get($key, $collection, $where = null)
    {
        $sql = sprintf(
            "select %s
            from %s",
            $this->sanitize($this->stringify($key, true, '`')),
            $this->sanitize($collection)
        );
        if (!is_null($where)) {
            foreach ($where as $key => $value) {
                $sql .= " WHERE ".$this->stringify($key, true, '`')."=".$this->stringfy($value);
            }
        }
    }

    /**
     * getAll
     *
     * Return all values in $collection
     *
     * @param String $collection Collection whose values are returned
     *
     * @return Array Values in collection
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function getAll($collection)
    {
        /* pending */
        return true;
    }

    /**
     * set
     *
     * Set value of $key or $collection/$key
     *
     * @param String $key        Name of parameter whose value is being set
     * @param Mixed  $value      Value of parameter
     * @param String $collection Collection in which $key will be set to $value
     *
     * @return boolean|null Status of assignment
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function set($key, $value, $collection)
    {
        /* pending */
        return true;
    }

    /**
     * sanitize
     *
     * Sanitizes queryString for Firebird
     *
     * @param string $queryString String to be sanitized
     *
     * @return string Sanitized queryString
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function sanitize($queryString)
    {
        return str_replace("\'", "''", $queryString);
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
