<?php
/**
 * Defines response object for AJAX and API returns
 **/
namespace Devtools;

class Response
{
    public $status;
    public $request;
    public $message;
    private $suppress_header;

    public function __construct($options=array())
    {
        $this->suppress_header = (isset($options['suppress_header']) ? $options['suppress_header'] : false);
        $this->status = 'OK';
        $this->request = $_REQUEST;
        $this->message = ($this->status ? "" : "Data could not be set\n");
    }

    /**
     * @param string $msg
     */
    public function message($msg, $error=false)
    {
        global $errorLog;

        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }

        $this->message .= "$msg\n";
        if($error) {
            $this->status = 'FAILED';
            trigger_error($msg);
        }
    }

    /**
     * @param string $msg
     */
    public function fail($msg)
    {
        $this->message($msg, true);
    }

    public function data($data)
    {
        if (empty($data)) {
            trigger_error(var_export(debug_backtrace(), true));
        }
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function json()
    {
        global $debugLog;
        if (!$this->suppress_header && !headers_sent()) {
            $debugLog->write('called!');
            $debugLog->write(debug_print_backtrace());
            var_dump(debug_print_backtrace());
            exit();
            header('Content-type: application/json');
        } else {
            $debugLog->write('not  called');
        }
        $debugLog->write(headers_list());
        return json_encode($this);
    }

    public static function ajax($url='', $request=array(), $dataOnly=true)
    {
        if (!empty($url)) {
            $reset = false;
            $temp = array();
            if (!empty($request)) {
                $reset = true;
                $temp = $_REQUEST;
                $_REQUEST = array();
                foreach ($request as $key => $value) {
                    $_REQUEST[$key] = $value;
                }
            }

            $_REQUEST['suppress_header'] = true;
            ob_start();
            include $_SERVER['DOCUMENT_ROOT'].$url;
            $data = ob_get_clean();
            $data = json_decode($data, true);

            if ($dataOnly && ($data['status']==='OK' || $data['status']===200)) {
                unset($data['status']);
                unset($data['message']);
                unset($data['request']);
                unset($data['suppress_header']);
            }

            if ($reset) {
                unset($temp['suppress_header']);
                $_REQUEST = $temp;
            }
            return $data;
        }
    }

    /**
     * load
     *
     * Loads result of SQL call into response object
     *
     * @param string $sql SQL query to return result
     *
     * @return array Array of results from SQL call
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function load($sql)
    {
        $data = array();
        if ($q=mysql_query($sql)) {
            if (!is_bool($q)) {
                if (($count=mysql_num_rows($q))>0) {
                    if ($count > 1) {
                        while ($result = mysql_fetch_assoc($q)) {
                            array_push($data, $result);
                        }
                    } else {
                        $row = mysql_fetch_assoc($q);
                        $this->data($row);
                    }
                } elseif ($err=mysql_error()) {
                    $this->message($err, true);
                } else {
                    $this->message('ID not found.', true);
                }
            } else {
                if ($insert_id = mysql_insert_id()) {
                    $this->data(array('insert_id'=>$insert_id));
                } else {
                    $this->data(array('update'=>true));
                }
            }
        } elseif ($err=mysql_error()) {
            $this->message($err, true);
        } else {
            $this->message('ID not found.', true);
        }
        if (isset($data) && !empty($data)) {
            $this->data($data);
        } elseif (isset($row) && !empty($row)) {
            $data = $row;
        } else {
            $data = false;
        }
        return $data;
    }

    /**
     * getSuppressHeader
     *
     * Checks request for suppress_header parameter
     *
     * @return bool Existence of suppress_header parameter
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function getSuppressHeader()
    {
        return array(
            'suppress_header' => (
                isset($_REQUEST['suppress_header'])
                    ? $_REQUEST['suppress_header']
                    : false
            )
        );
    }

    /**
     * getRequest
     *
     * Returns array based on _REQUEST
     *
     * @param array $validParams Array of valid parameters and their defaults
     *
     * @return array Values from request or fallback defaults
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function getRequest($validParams = array())
    {
        $request = empty($validParams) ? (array)$_REQUEST : array();
        foreach ($validParams as $param=>$default) {
            if (is_numeric($param)) {
                $param = $default;
                $default = null;
            }
            $request[$param] = isset($_REQUEST[$param])
                ? $_REQUEST[$param]
                : $default;
        }
        return $request;
    }
}
