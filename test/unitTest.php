<?php

namespace seagoj\devtools;
require_once('../lib/autoload/src/autoload.php');

class unitTest extends dbgTest {
	private $unit;
	
	public function __construct() {
		$this->unit = new unit();
		$this->testing($this->unit);
		$this->with($this);
		$this->runTest();
	}
	
	public function __constructTest() {
		return true;
	}
	public function testingTest() {
		$this->unit->testing($this->unit);
		return true;
	}
public function withTest() {
		$this->unit->with($this);
		return true;
	}
	/*
	 * @depends testing
	 * @depends with
	 */
	public function runTestTest() {
		return true;
	}
	public function resultsTest() {
		return true;
	}
	public function __destructTest() {
		return true;
	}
	public function buildDependsTest() {
		return true;
	}
	public function getCommentTagsTest() {
		return true;
	}
	public function getUnitTagsTest() {
		return true;
	}
	public function scanCommentsForTagsTest() {
		return true;
	}
	public function msgTest() {
		return true;
	}
}

new unitTest;