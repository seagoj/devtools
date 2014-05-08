<?php
class GetPatientNamesTest extends PHPUnit_Framework_TestCase
{
    private $path;

    public function setup()
    {
        $this->path = 'efax.bpspharmacy.com/html/secure/orders/getPatientNames.php';
    }

    public function testGetPatientNamesString()
    {
        $this->loadRequest(['q' => 'SPARKS']);
        $response = $this->call();

        foreach ($respone as $line) {
            if ($line !== '') {
                $columns = explode("|", $line);
                $this->assertEquals($columns[0], ($columns[2].", ".$columns[1]));
            }
        }

        $this->unloadRequest();
    }

    public function testGetPatientNamesJSON()
    {
        $this->loadRequest(['q' => 'SPARKS', 'type' => 'json']);
        $response = $this->call();
        $this->assertEquals('OK', $response->status);
        $this->unloadRequest();
    }

    private function call()
    {
        ob_start();
        require $this->path;
        $response = ob_get_clean();
        return $this->format($response);
    }

    private function format($response)
    {
        switch($_REQUEST['type'])
        {
            case '':
            case 'string':
                $ret = explode("\n", $response);
                break;
            case 'json':
                $ret = json_decode($response);
                break;
            default:
                throw new \InvalidArgumentException($_REQUEST['type']." is not a valid response type.");
                break;
        }

        return $ret;
    }

    private function loadRequest($values)
    {
        foreach ($values as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }

    private function unloadRequest()
    {
        unset($_REQUEST);
        $this->assertTrue(!isset($_REQUEST));
    }
}
