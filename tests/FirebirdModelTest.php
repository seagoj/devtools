<?php
class FirebirdModelTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function teardown()
    {
    }

    /**
     * @covers Devtools\FirebirdModel::__construct
     **/
    public function testFirebirdModel()
    {
        $this->assertInstanceOf("Devtools\FirebirdModel", new \Devtools\FirebirdModel());
    }

    /**
     * @covers Devtools\FirebirdModel::__construct
     **/
/*    public function testOptions()
    {
        $this->assertInstanceOf("Devtools\FirebirdModel", new \Devtools\FirebirdModel(array("location"=>"il")));
    }
*/

    /**
     * @covers Devtools\FirebirdModel::__construct
     * @expectedException Exception
     **/
    public function testConnectionFailure()
    {
        new \Devtools\FirebirdModel(["host"=>"NotARealServer"]);
    }

    /**
     * @covers Devtools\FirebirdModel::query
     **/
    public function testQuery()
    {
        $limit = 20;
        $fb = new \Devtools\FirebirdModel();
        $results = $fb->query("select FIRST $limit * from PATIENT order by PATIENT_ID DESC");
        $this->assertFalse($results === null);
    }

    /**
     * @covers Devtools\FirebirdModel::getLastPatients
     **/
    public function testGetLastPatients()
    {
        $limit = 20;
        $fb = new \Devtools\FirebirdModel();
        $results = $fb->getLastPatients($limit);
        $this->assertFalse($results === null);
    }

    /**
     * @covers Devtools\FirebirdModel::getPatientInfoByTelephoneRxID
     **/
    public function testGetPatientInfoByTelephoneRxID()
    {
        $fb = new \Devtools\FirebirdModel();
        $validID = $fb->query("select first 1 TELEPHONERX_ID from TELEPHONERX");
        $patientInfo = $fb->getPatientInfoByTelephoneRxID($validID);
    }
}
