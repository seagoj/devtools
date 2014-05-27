<?php

namespace Devtools;

class Rest
{
    public $method;
    public $request;
    public $parameters;
    public $response;
    private $options;
    private $id;
    private $debugLog;

    public function __construct($options)
    {
        $this->debugLog = \Devtools\Log::debugLog();
        $this->response = new \Devtools\Response;

        $this->options = array_merge(
            array(
                'type' => null
            ),
            $options
        );

        $this->setMethod();
        $this->setRequest();
        $this->setParameters();
        if (!empty($this->options['type'])) {
            $this->process();
        }
    }

    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function setRequest()
    {
        $requestURI = explode('?', $_SERVER['REQUEST_URI']);
        $this->request = explode('/', substr($requestURI[0], strlen($this::getRoot())));
    }

    private function setParameters()
    {
        $parameters = !empty($_REQUEST) ? $_REQUEST : null;
        $temp = array();
        if (!empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $value = explode(',', $value);
                if (is_array($value)) {
                    $temp2 = array();
                    foreach ($value as $v) {
                        array_push($temp2, $this->quote($v));
                    }
                    $value = $temp2;
                }
                $temp[$key] = $value;
            }
        }
        $this->parameters = $temp;
    }

    private function process()
    {
        switch ($this->options['type']) {
        case 'mysql':
            if (!empty($this->request)) {
                switch ($this->method) {
                case 'GET':
                    $table = array_shift($this->request);
                    $sql = $this->buildSQL($table);

                    if ($q=mysql_query($sql)) {
                        $data = array();
                        while ($row = mysql_fetch_assoc($q)) {
                            array_push($data, $row);
                        }
                        $this->response->data($data);
                    } else {
                        $this->response->fail('Data not found.');
                    }
                    break;
                }
            } else {
                $this->response->fail('Invalid request.');
            }
            break;
        case 'firebird':
            if (!empty($this->request) && count($this->request)>=2) {
                $pharmacy = array_shift($this->request);
                $table = array_shift($this->request);
                $sql = $this->buildSQL($table);

                $this->debugLog->write($sql);
                $fb = getFirebirdModel($pharmacy);
                $this->response->data($fb->query($sql));
            } else {
                $this->response->fail('Invalid request.');
            }
            break;
        }
    }

    private static function getRoot()
    {
        $first_dir = explode('/', $_SERVER['REQUEST_URI']);
        $first_dir = $first_dir[1];
        $path_array = explode('/', getCWD());
        $path = array();

        for ($p = 0; $p<count($path_array); $p++) {
            if ($path_array[$p] === $first_dir) {
                break;
            }
        }

        for ($p; $p<count($path_array); $p++) {
            array_push($path, $path_array[$p]);
        }

        return '/'.implode('/', $path).'/';
    }

    private function getCols()
    {
        if (!empty($this->request)) {
            if (is_numeric($this->request[0])) {
                $this->id = $this->request[0];
            } else {
                $cols = array();
                for ($i=0; $i<count($this->request); $i++) {
                    array_push($cols, $this->request[$i]);
                }
                $cols = implode(',', $cols);
            }
        }

        return !empty($cols) ? $cols : '*';
    }

    private function buildSQL($table)
    {
        $cols = $this->getCols();

        $sql = sprintf(
            "SELECT %s
            FROM %s",
            mysql_real_escape_string($cols),
            mysql_real_escape_string($table)
        );

        if (isset($this->id)) {
            $sql .= sprintf(
                " WHERE %s=%s",
                mysql_real_escape_string($table.'_id'),
                mysql_real_escape_string($this->id)
            );
        } else if (!empty($this->parameters)) {
            $sql .= " WHERE ";
            $first = true;
            foreach ($this->parameters as $key=>$value) {
                if (!$first) {
                    $sql .= " AND ";
                } else {
                    $first = false;
                }
                $sql .= $key;

                if (is_array($value)) {
                    $operand = ' IN ';
                    $value = '('.implode(',', $value).')';
                } else {
                    $operand = '=';
                }

                $sql .= $operand.$value;
            }
        }

        return $sql;
    }

    private function quote($data)
    {
        return is_numeric($data) ? $data : "'$data'";
    }

    public static function call($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($response=curl_exec($curl)) {
            var_dump($response);
            curl_close($curl);
            return json_decode($response);
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl);
            trigger_error($info);
        }
    }

    public static function getRequest()
    {
        $requestURI = explode('?', $_SERVER['REQUEST_URI']);
        return explode('/', substr($requestURI[0], strlen($this::getRoot())));
    }
}
