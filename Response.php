<?php

namespace Devtools;

/**
 * Defines response object for AJAX and API returns
 **/
namespace Devtools;

class Response
{
    public $status;
    public $request;
    public $message;
    private $suppres_header;

    public function __construct($options=array())
    {
        $this->suppress_header = (isset($options['suppress_header']) ? $options['suppress_header'] : false);
        $this->status = 'OK';
        $this->request = $_REQUEST;
        $this->message = ($this->status ? "" : "Data could not be set\n");
    }

    public function message($msg, $error=false)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }

        $this->message .= "$msg\n";
        if($error) $this->status = 'FAILED';
    }

    public function data($data)
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function json()
    {
        if (!$this->suppress_header && !headers_sent()) {
            header('Content-type: application/json');
        }
        return json_encode($this);
    }

    public static function ajax($url='', $request=array(), $dataOnly=true)
    {
        if (!empty($url)) {
            $reset = false;
            if (!empty($request)) {
                $reset = true;
                $temp = $_REQUEST;
                $_REQUEST = array();
                foreach($request as $key => $value) {
                    $_REQUEST[$key] = $value;
                }
            }

            $_REQUEST['suppress_header'] = true;
            ob_start();
            require $_SERVER['DOCUMENT_ROOT'].$url;
            $data = ob_get_clean();
            /* var_dump($data); */
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

    public function load($sql) {
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

    public static function getSuppressHeader() {
        return array('suppress_header'=>(isset($_REQUEST['suppress_header']) ? $_REQUEST['suppress_header'] : false));
    }
}
