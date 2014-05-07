<?php

namespace Devtools;

class Rest
{
    public $method;
    public $request;
    public $parameters;
    public $response;
    private $options;

    public function __construct($options) {
        $defaults = array(
            'type' => null
        );

        $this->options = array_merge($defaults, $options);
        $this->response = new \Devtools\Response;
        $this->setMethod();
        $this->setRequest();
        $this->setParameters();
        if (!empty($this->options['type'])) {
            $this->processCall();
        }
    }

    private function setMethod() {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function setRequest() {
        $requestURI = explode('?', $_SERVER['REQUEST_URI']);
        $this->request = explode('/', substr($requestURI[0], strlen($this->getRoot())));
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

    private function processCall() {
        switch ($this->options['type']) {
        case 'mysql':
            if (!empty($this->request)) {
                $sql = '';
                $cols = '*';
                switch ($this->method) {
                case 'GET':
                    $table = $this->request[0];
                    if (count($this->request)>1) {
                        if (is_numeric($this->request[1])) {
                            $id = $this->request[1];
                        } else {
                            $cols = array();
                            for ($i=1; $i<count($this->request); $i++) {
                                array_push($cols, $this->request[$i]);
                            }
                            $cols = implode(',', $cols);
                        }
                    }

                    $sql = sprintf(
                        "SELECT %s
                        FROM %s",
                        mysql_real_escape_string($cols),
                        mysql_real_escape_string($table)
                    );

                    if (isset($id)) {
                        $sql .= sprintf(
                            " WHERE %s=%s",
                            mysql_real_escape_string($table.'_id'),
                            mysql_real_escape_string($id)
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
        }
    }

    private function getRoot() {
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

    private function quote($data) {
        return is_numeric($data) ? $data : "'$data'";
    }

    public static function call($url) {
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
}
