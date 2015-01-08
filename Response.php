<?php namespace Devtools;

class Response implements IService// , \Serializable
{
    public $status;
    public $request;
    public $message;
    private $suppressHeader;
    private $data;
    private $publicProperties;
    private $repository;

    public function __construct(Repository $repository = null)
    {
        $this->suppressHeader = $this->isSuppressHeader();
        $this->status = 'OK';
        $this->request = $this->getRequest();
        $this->message = $this->status ? "" : "Data could not be set\n";
        $this->publicProperties = getProperties($this);
        $this->repository = $repository;
    }

    public function __get($property)
    {
        return $this->data[$property];
    }

    public function __set($property, $value)
    {
        return $this->data[$property] = $value;
    }

    public function __call($method, $params)
    {
        switch($method) {
        case 'getSuppressHeaders':
        case 'getSuppressHeader':
            $this->isSuppressHeader($params);
            break;
        default:
            throw new \Exception("Method $method is not defined.");
            break;
        }
    }

    public static function __callStatic($method, $params)
    {
        switch($method) {
        case 'getSuppressHeaders':
        case 'getSuppressHeader':
            Response::isSuppressHeader($params);
            break;
        default:
            throw new \Exception("Method $method is not defined.");
            break;
        }
    }

    public function __sleep()
    {
        if (is_null($this->publicProperties)) {
            $this->publicProperties = array();
        }

        return !is_null($this->data)
            ? array_merge($this->publicProperties, array_keys($this->data))
            : $this->publicProperties;
    }

    public function processRequest()
    {
        var_dump(__METHOD__);
        if ($this->isApiCall()) {
            $this->api();
        }
    }

    public function message($msg, $error=false)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }
        $this->message .= "$msg\n";
        if ($error) {
            $this->status = 'FAILED';
            /* trigger_error($msg); */
        }
    }

    public function fail($msg, $throwException = false)
    {
        $this->message($msg, true);
        if ($throwException) {
            throw new \Exception($msg);
        }
    }

    public static function ajax($url='', $request=array(), $dataOnly=true)
    {
        if (!empty($url)) {
            $reset = false;
            $temp = array();
            if (!empty($request)) {
                $reset = true;
                $temp = isset($_REQUEST) ? $_REQUEST : array();
                $_REQUEST = array();
                foreach ($request as $key => $value) {
                    $_REQUEST[$key] = $value;
                }
            }
            $_REQUEST['suppress_header'] = true;
            ob_start();
            include $_SERVER['DOCUMENT_ROOT'].$url;
            $data = json_decode(ob_get_clean(), true);
            if ($dataOnly
                && in_array($data['status'], array('OK', 200))
            ) {
                unset(
                    $data['status'],
                    $data['message'],
                    $data['request'],
                    $data['suppress_header']
                );
            }
            if ($reset) {
                unset($temp['suppress_header']);
                $_REQUEST = $temp;
            }
            return $data;
        }
    }

    /* DEPRECATED */
    public function load($sql, $params = null)
    {
        if (is_null($this->repository)) {
            throw new \Exception('No repository available.');
        }

        $data = $this->repository->query($sql, $params, true);
        return $this->data = is_array($data) ? $data : array('data' => $data);
    }

    public static function isSuppressHeader()
    {
        return array(
            'suppress_header' => (
                isset($_REQUEST['suppress_header'])
                    ? $_REQUEST['suppress_header']
                    : false
            )
        );
    }

    // =====INTERFACES=====
    public function data(array $data)
    {
       if (empty($data)) {
           trigger_error(var_export(debug_backtrace(), true));
       }
       foreach ($data as $key => $value) {
           $this->$key = $value;
       }
       return $this;
    }

    public static function getRequest($validParams = null)
    {
        if (is_null($validParams)) {
            return (array)$_REQUEST;
        }
        $request = array();
        foreach ($validParams as $param => $default) {
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

    public function delimited($options = null)
    {
        $default = array(
            'rowDelim' => "\n",
            'colDelim' => "|"
        );

        if (!is_null($options)) {
            $options = array_merge(
                $default,
                $options
            );
        } else {
            $options = $default;
        }

       $response = '';
       foreach ($this->data as $row) {
           $rowStr = '';
           foreach ($row as $value) {
               $rowStr .= $rowStr==='' ?
                   $value :
                   ($options['colDelim'].$value);
           }
           $response .= ($rowStr.$options['rowDelim']);
       }
       return $response;
    }

    public function json($dataOnly = false)
    {
        $suppressHeader = $this->isSuppressHeader();
        if (!$suppressHeader['suppress_header'] && !headers_sent($file, $line)) {
            header('Content-type: application/json');
        }

        return json_encode($this->php());
    }

    public function php($serialize = false)
    {
       $ret = array();
       $sleep = $this->__sleep();
       foreach ($sleep as $property) {
           $ret[$property] = $this->$property;
       }
       return $serialize ? serialize($ret) : $ret;
    }

    public function serialize()
    {
        return $this->php(true);
    }

    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $key => $value) {
            if (in_array($key, $this->publicProperties)) {
                $this->$key = $value;
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    public function api()
    {
        var_dump(__METHOD__);
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \Exception("Method {$method} not yet implemented.");
    }

    public static function isApiCall()
    {
        var_dump(__METHOD__);
        return self::isAjax()
            && !isset($_REQUEST['phpspec']);
    }

    public static function isAjax()
    {
        var_dump(__METHOD__);
        var_dump($_SERVER);
        var_dump($_SERVER['HTTP_X_REQUESTED_WITH']);
        return isset($_SERVER)
            && isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH']  === 'XMLHttpRequest';
    }

    public static function isNotTest()
    {
        return !isset($_REQUEST['phpspec']);
    }

    protected function loadRequest($defaults)
    {
        $request = $this->getRequest($defaults);
        foreach ($request as $key => $value) {
            $this->$key = $value;
        }
    }
}

function getProperties($object)
{
    return array_keys(
        get_object_vars($object)
    );
}
