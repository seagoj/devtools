<?php
/**
 * Model: Model library for PHP
 * 
 * @name      Model
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 */
namespace Devtools;

/**
 * Model class for personal MVC framework
 * Only class with knowledge of the database connections
 *
 * @author jds
 */

class Model
{
    private $id;
    private $conn;
    private $dbg;
    private $tbl;
    private $dbtype;
    private $cols;
    private $where;
    private $values;
    private $updateOnDup;
    private $rowCount;
    private $query;
    private $config;

    /*
     * Initiates model and set default database and query type, if passed
     * @done  validates passed database type
     * @done  pulls credentials from VCAP_SERVICES Environment Variable
     * @done  sets model.conn to connection object based on DB type
     * @action  sets model.tbl to default table, if passed
     * @action  sets model.query to default query type, if passed
     * @param       $tbl   STRING  Choose which table in the database to act upon
     * @param       $query STRING  Set action to work on the table
     * @param       $build BOOL    If true, create table if table doesn't exist
     * @return void
     */
    public function __construct ($id, $tbl=NULL, $query=NULL, $dbtype='mysql')
    {
    	$this->config = new \Devtools\Config($this);
    	
        $this->setDbg(_DBG_);
        if(_DBG_) dbg::msg("dbg is in scope of model class");

        if ($this->setDBType($dbtype)) {
            $this->id = $id;

            $this->setCredentials();

            switch ($this->dbtype) {
                case 'mysql':
                    $this->dbgMsg("Using mysql connection type.", __METHOD__);
                    if(!is_null(_DB_PORT_))
                        $server = _DB_HOST_.':'._DB_PORT_;
                    else
                        $server = _DB_HOST_;
                    $this->conn = mysql_connect($server, _DB_USER_, _DB_PASSWORD_);
                    print mysql_error();
                    break;
                case 'mongo':
                    $this->dbgMsg("Using mongo connection type.", __METHOD__);
                    /* @todo    initialize connection to mongoDB */
                    $this->conn = new Mongo($server);
                    if(!$this->conn) throw Exception("Failed to connect to MongoDB at ".$server);
                    /* end todo */
                    break;
                case 'nosql':
                    $this->dbgMsg("Using nosql connection type.", __METHOD__);
                    /* @todo   initialize connection to NoSQL */

                    /* end todo */
                    break;
                default:
                    $this->dbgMsg($this->dbtype." is not a supported database type.", __METHOD__, true);
                    break;
            }

            $this->dbgMsg("model.conn opened", __METHOD__);

            // set model.tbl to current table if it is passed on object initialization
            if(!is_null($tbl))
                $this->from($tbl);

            // set model.type to current type if it is passed on object initialization
            if(!is_null($query))
                $this->setQuery($query);
        } else
            $this->dbgMsg($dbtype." is not a supported database type.", __METHOD__, true);
    }
    public function __destruct ()
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        mysql_close($this->conn);
        $this->dbgMsg("model.conn closed", __METHOD__);
    }


    public function from ($tbl)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        if($this->dbtype=='mysql')
            $tbl = '`'._DB_NAME_.'`.`'.$tbl.'`';

        $this->tbl = $this->validateTbl($tbl);

        $this->dbgMsg("model.tbl set to $tbl", __METHOD__);
    }
    public function columns ($cols='*')
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        $this->validateCols($cols);
    }
    public function where ($condition, $conjunction=NULL)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        try {
            $this->validateWhere($condition, $conjunction);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function setQuery ($query)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        $query = strtolower($query);
        switch ($query) {
            case 'insertupdate':
                $this->query = 'INSERT';
                $this->updateOnDup=true;
                break;
            case 'default':
                if (!$this->dbg) {
                    $this->query = NULL;
                    $this->dbgMsg("model.query set to $query, but not in debug mode.", __METHOD__, true);
                }
                break;
            case 'update':
            case 'insert':
            case 'select':
                $this->query = $query;
                break;
            default:
                $this->dbgMsg("$query is not a valid query type.", __METHOD__, true);
                break;
        }
        $this->dbgMsg("model.query set to $query", __METHOD__);
    }
    public function query($retType='assoc')
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        $validRetTypes = array(
            'array',
            'assoc',
            'field',
            'lengths',
            'object',
            'row'
            );

        if (in_array($retType, $validRetTypes)) {
            $sql = $this->assemble();
            $result = mysql_query($sql);
            $this->rowCount = mysql_num_rows($result);
            print mysql_error();
            $this->dbgMsg("Return query result.", __METHOD__);



            switch ($retType) {
                case 'array':
                    $ret = mysql_fetch_array($result);
                    break;
                case 'assoc':
                    $this->dbgMsg("Return type is assoc", __METHOD__);
                    // Begin Development Section

                    //if ($this->rowCount > 1) {
                        $ret =array();
                        $count = 0;


                        while ($row=mysql_fetch_assoc($result)) {
                            if (is_array($this->cols)) {
                                $var = $row[$this->cols[0]];
                                $value = $row[$this->cols[1]];
                                if($value==NULL)
                                    $new = array('value'=>$var);
                                else
                                    $new = array($var=>$value);
                                $ret = $ret + $new;
                                /*
                                foreach ($this->cols AS $key) {
                                    //$key = $col;
                                    //$key = $row['name'];
                                    $ret[$key] = $row['value'];
                                }
                                 *
                                 */
                            } else {
                                $key = $this->cols;
                                $ret[$key] = $row['value'];
                            }
                        }
                    //}
                    //else
                    //    $ret = mysql_fetch_assoc($result);

                    // End Development Section
                    $this->dbgMsg("Result fetched.", __METHOD__);
                    break;
                case 'field':
                    $ret = mysql_fetch_field($result);
                    break;
                case 'lengths':
                    $ret = mysql_fetch_lengths($result);
                    break;
                case 'object':
                    $ret = mysql_fetch_object($result);
                    break;
                case 'row':
                    $ret = mysql_fetch_row($result);
                    break;
             };
            $this->reset();

            return $ret;
        } else
            $this->dbgMsg("Return type must be ".implode(', ', $validRetTypes).".", __METHOD__, true);
    }
    public function values($vals)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        $this->validateVals($vals);
    }
    public function assemble ()
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        if (is_array($this->cols)) {
            foreach ($this->cols AS $col) {
                ++$count==1 ? $colsStr = '`'.$this->mysqlSanitize($col).'`' : $colsStr .= ",`".$this->mysqlSanitize($col)."`";
                /*
                if(!isset($colStr))
                    $colsStr = '`'.$this->mysqlSanitize($col).'`';
                else
                    $colsStr .= ",`".$this->mysqlSanitize($col)."`";
                 *
                 */
            }
        } else {
            if(!isset($colStr))
                $colsStr = '`'.$this->mysqlSanitize($this->cols).'`';
            else
                $colsStr .= ",`".$this->mysqlSanitize($this->cols)."`";
        }
        switch ($this->query) {
            case 'select':
                return "SELECT $colsStr FROM $this->tbl WHERE $this->where";
                break;
            case 'insert':
                if(!isset($this->tbl)) throw Exception("Table not set.");
                if(!isset($colsStr)) throw Exception("Cols not set.");
                if(!isset($this->values)) throw Exception("Values not set.");
                if($this->updateOnDup && !isset($this->where)) throw Exception("Where not set.");

                $sql = "INSERT INTO $this->tbl ($colsStr) VALUES ($this->values)";
                $this->updateOnDup ? $sql .= " ON DUPLICATE KEY UPDATE $this->where" : $sql .= '';

                return $sql;
                break;
            case 'update':
                $valsArr = explode(',', $this->values);

                if(count($this->cols)!=count($valsArr))
                    throw Exception("number of columns != number of values");
                else {
                    for ($i=0; $i<count($this->cols); $i++) {
                        if(!isset($sql))
                            $sql = "$this->cols[$i]=$valsArr[$i]";
                        else
                            $sql .= ",$this->cols[$i]=$valsArr[$i]";
                    }

                    return "UPDATE $this->tbl SET $sql WHERE $this->where";
                }
                break;
            case 'default':
                    /*CREATE TABLE  `464119_nxtlvl`.`test` (
                     *  `index` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                     *  `name` VARCHAR( 50 ) NOT NULL ,
                     *  `sql` VARCHAR( 1000 ) NOT NULL
                     *) ENGINE = INNODB
                     */

                    return "CREATE TABLE IF NOT EXISTS $this->tbl ( `index` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `name` VARCHAR( 50 ) NOT NULL , `sql` VARCHAR( 1000 ) NOT NULL) ENGINE = INNODB";
                break;
        }
    }
    public function numRows ()
    {
        return $this->rowCount;
    }

    private function setDbg()
    {
        /*
         * @TODO Trigger DBG from config file
         */
        $this->dbg = _DBG_;
        //if($this->dbg) require_once(_LIB_PATH_."/lib.dbg/src/dbg.php");
        if(_DBG_) {
        	$this->dbg = new dbg();
        	dbg::msg("DBG loaded");
        }
    }
    private function dbgMsg($msg, $method, $exception=false)
    {
        if($this->dbg) dbg::msg($msg, $method, $exception);
    }
    private function setDBType($dbtype)
    {
        /* @todo    set these values as env variables */
        $validDB = array(
            'mysql' => true,
            'mongo' => false,
            'nosql' => false
            );

        $dbtype = strtolower($dbtype);

        if($validDB[$dbtype])
            $this->dbtype = $dbtype;
        else {
            throw Exception("$dbtype is not a supported database type.");
            $this->dbgMsg("$dbtype is not a supported database type.", __METHOD__, true);
        }

        return (isset($this->dbtype));
    }
    private function validateTbl($tbl)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        switch ($this->dbtype) {
            case 'mysql':
                $pattern = "/`([^`]*)`\.`([^`]*)`/";

                if (preg_match_all($pattern, $tbl, $tokens)) {
                    $dbTok = $tokens[1][0];
                    $tblTok = $tokens[2][0];

                return '`'.$this->mysqlSanitize($dbTok).'`.`'
                    .$this->mysqlSanitize($tblTok).'`';
                } else $this->dbgMsg("$tbl is not a valid `db`.'tableName'", __METHOD__, true);
                break;
            case 'mongo':
                /* @todo     */
                $this->dbgMsg("MongoDB is not yet supported.", __METHOD__, true);
                /* end todo */
                break;
            case 'nosql':
                /* @todo     */
                $this->dbgMsg("NoSQL is not yet supported.", __METHOD__, true);
                /* end todo */
                break;
            default:
                $this->dbgMsg("$this->dbtype is not supported.", __METHOD__, true);
                break;
        }
    }
    private function validateCols($cols)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        /*
        $pattern = "/`([^`]*)`,?/";

        if (preg_match_all($pattern, $cols, $tokens)) {
            foreach ($tokens[1] AS $col) {
                if(!isset($this->cols))
                    $this->cols = '`'.$this->mysqlSanitize($col).'`';
                else
                    $this->cols .= ",`".$this->mysqlSanitize($col)."`";
            }
        } else
            $this->dbgMsg("$cols is not a valid `col1`,`col2`", __METHOD__, true);
         */

        switch ($this->dbtype) {
            case 'mysql':
                $this->cols = $cols;
                break;
            default:
                $this->dbgMsg("$this->dbtype is not a supported database type", __METHOD__, true);
                break;
        }

            if($this->cols!=NULL)
                $this->dbgMsg("model.cols set to $this->cols", __METHOD__);
            else
                $this->dbgMsg("Failed to set model.cols", __METHOD__, true);
    }
    private function validateWhere($where, $conjunction)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        /*
        if ($where!==true) {
            $pattern = "/`([^`]*)`(\=| LIKE )'([^`]*)'/";

            if (preg_match_all($pattern, $where, $tokens)) {
                $validConjuntion = array('and','AND','or','OR');

                if(strpos($where, ' LIKE '))
                    $operator = ' LIKE ';
                else
                    $operator = '=';

                $column = $tokens[1][0];
                $value = $tokens[3][0];

                if(!isset($this->where))
                    $this->where = "`".$this->mysqlSanitize($column)."`".$operator."'".$this->mysqlSanitize($value)."'";
                else {
                    if(in_array($validConjuction, $conjunction))
                        $this->where .= " $conjunction ".$condition;
                    else throw Exception("Invalid conjunction: $conjunction");
                }
            } else
                $this->dbgMsg("$where is not a valid `column`='value'", __METHOD__, true);
      } else
        $this->where=true;
         */

        print $conjuction;

        switch ($this->dbtype) {
            case 'mysql':
                foreach ($where AS $set) {
                    ++$count==1 ? $conditional.="" : $conditional.=" $conjunction ";
                    $conditional .= '`'.$set['col'].'`=\''.$set['val'].'\'';
                }
                break;
        }

        $this->where = $conditional;
        $this->dbgMsg("model.where set to $this->where", __METHOD__);
    }
    private function validateVals($vals)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        $pattern = "/'([^']*)',?/";

        if (preg_match_all($pattern, $vals, $tokens)) {
            foreach ($tokens[1] AS $val) {
                if(!isset($this->values))
                    $this->values = "'".$this->mysqlSanitize($val)."'";
                else
                    $this->values .= ",'".$this->mysqlSanitize($val)."'";
            }
        } else
            $this->dbgMsg("$vals is not a valid `val1`,`val2`", __METHOD__, true);
    }
    private function mysqlSanitize($dirty)
    {
        $this->dbgMsg("Initialized", __METHOD__);
        /*****************************************/

        $clean = mysql_real_escape_string($dirty, $this->conn);
        if ($clean) {
            $this->dbgMsg("Return $clean", __METHOD__);

            return $clean;
        } else $this->dbgMsg("Attempt to sanitize $dirty failed.", __METHOD__, true);
    }
    private function reset()
    {
        //UNSET($this->tbl);
        UNSET($this->query);
        UNSET($this->cols);
        UNSET($this->where);
        UNSET($this->values);
        UNSET($this->updateOnDup);
    }
    private function setCredentials()
    {
        switch ($this->dbtype) {
            case 'mysql':
                $services = getenv("VCAP_SERVICES");
                dbg::dump(getenv("VCAP_SERVICES"));
                if ($services) {
                    $services_json = json_decode($services,true);
                    $mysql_config = $services_json["mysql-5.1"][0]["credentials"];

                    define('_DB_NAME_', $mysql_config["dbname"]);
                    define('_DB_USER_', $mysql_config["user"]);
                    define('_DB_PASSWORD_', $mysql_config["password"]);
                    define('_DB_HOST_', $mysql_config["hostname"]);
                    define('_DB_PORT_', $mysql_config["port"]);
                } else if(!_CONFIG_)
                    $this->dbgMsg("Database credentials could not be discovered.", __METHOD__, true);
                break;
            case 'mongo':
                break;

        }

    }

    public function UNIT()
    {
        $this->dbgMsg("BoF ".rand(), __METHOD__);

        $this->dbgMsg("EoF", __METHOD__);

        /*
         *  public function unitTest()
    {
        try {
            $select = new model();
            $select->from('`464119_nxtlvl`.`config`');
            $select->type('SELECT');
            $select->columns('`value`');
            $select->where("`variable`='companyName'");
            $valid = "SELECT `value` FROM `464119_nxtlvl`.`config` WHERE `variable`='companyName'";
            if($select->assemble() != $valid)
                print "SELECT method failed unit test.";
            else
                print "SELECT method passed unit test.";

            $insert = new model();
            $insert->from('`464119_nxtlvl`.`config`');
            $insert->type('INSERT');
            $insert->columns('`variable`,`value`');
            $insert->values("'companyName','test'");
            $valid = "INSERT INTO `464119_nxtlvl`.`config` (`variable`,`value`) VALUES ('companyName','test')";
            if($insert->assemble() != $valid)
                print "INSERT method failed unit test.";
            else
                print "INSERT method passed unit test.";

            $update = new model();
            $update->from('`464119_nxtlvl`.`config`');
            $update->type('UPDATE');
            $update->columns('`variable`,`value`');
            $update->values("'companyName','test'");
            $update->where(true);
            $valid = "UPDATE `464119_nxtlvl`.`config` SET `variable`='companyName',`value`='test' WHERE 1";
            if($update->assemble() != $valid)
                print "UPDATE method failed unit test.";
            else
                print "UPDATE method passed unit test.";
        } catch (Exception $e) {
            print $e;
        }
    }

         */
    }
}

if (_DBG_) {
    $model = new model('portfolio','dbconfig','select');
    dbg::dump($model->assemble());
}

/*
if (_DEBUG_) {
    //@TODO trigger Unit testing from config file

    //$model = new model('portfolio','dbconfig','select');
    // print $model->assemble();
    //$model->query();
    //$model->UNIT();
}
*/
