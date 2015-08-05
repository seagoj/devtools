<?php namespace Devtools;

use Exception;

class Rest extends Response
{
    public $method;
    public $request;
    public $parameters;
    public $response;
    private $options;
    private $id;
    private $log;

    public function __construct(
        PDORepository $repository,
        Log $log
    ) {
        $this->repository = $repository;
        $this->log        = $log;

        $this->setMethod();
        $this->setRequest();
        $this->setParameters();

        if (!isset($_SERVER['phpspec'])) {
            $this->process();
        }
    }

    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function setRequest()
    {
        $this->request = $this->getRequest();
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
                    $value = count($temp2) > 1
                        ? $temp2
                        : $temp2[0];
                }
                $temp[$key] = $value;
            }
        }
        $this->parameters = $temp;
    }

    public function process()
    {
        if (empty($this->request)) {
            throw new Exception('Invalid request.');
        }

        switch ($this->method) {
        case 'GET':
            $table = array_shift($this->request);
            extract($this->buildGetSQL($table));

            $result = $this->repository->query($sql, $params, true);
            if (!$result) {
                throw new Exception('Data not found.');
            }

            $this->data($result);
            return $result;
            break;
        }
        /* case 'firebird': */
        /*     if (!empty($this->request) && count($this->request)>=2) { */
        /*         $pharmacy = array_shift($this->request); */
        /*         $table = array_shift($this->request); */
        /*         $sql = $this->buildSQL($table); */
        /*         $this->log->write($sql); */
        /*         $fb = getFirebirdModel($pharmacy); */
        /*         $this->data($fb->query($sql)); */
        /*         return $fb->query($sql); */
        /*     } else { */
        /*         $this->fail('Invalid request.'); */
        /*     } */
        /*     break; */
        /* } */
    }

    public static function getRoot()
    {
        $cwd = isset($_SERVER['phpspec']) ? 'api' : getCWD();

        $first_dir = explode('/', $_SERVER['REQUEST_URI']);
        $first_dir = $first_dir[1];
        $path_array = explode('/', $cwd);
        $path = array();
        $count = count($path_array);
        for ($p = 0; $p < $count; $p++) {
            if ($path_array[$p] === $first_dir) {
                break;
            }
        }
        for ($p; $p < $count; $p++) {
            array_push($path, $path_array[$p]);
        }

        return '/'.implode('/', $path).'/';
    }

    private function getCols()
    {
        if (!empty($this->request)) {
            $count = count($this->request);
            if (is_numeric($this->request[0])) {
                $this->id = $this->request[0];
            } else {
                $cols = array();
                for ($i=0; $i < $count; $i++) {
                    array_push($cols, $this->request[$i]);
                }
                $cols = implode(',', $cols);
            }
        }
        return !empty($cols) ? $cols : '*';
    }

    private function buildGetSQL($table)
    {
        $cols = $this->getCols();

        $sql = "SELECT {$cols}
            FROM {$table}";
        $params = array();

        if (isset($this->id)) {
            $sql .= " WHERE {$table}_id=:id";
            $params['id'] = $this->id;
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
                $sql .= $operand.':'.$key;
            }
        }

        return array('sql' => Format::stripWhitespace($sql), 'params' => $params);;
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
        return explode('/', substr($requestURI[0], strlen(Rest::getRoot())));
    }
}
