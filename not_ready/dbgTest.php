<?php
/**
 * unit.php: Unit Tests for the Debug library
 * 
 * @TODO	Comment Unit Tests in accordance with PHPDoc standard
 */

namespace seagoj\devtools;
require_once('../lib/autoload/src/autoload.php');

class dbgTest extends unit {
	private $unit;
	private $dbg;

	public function __construct($class=NULL) {
		if($class!=NULL)
			$this->dbg = new dbg($class);
		else
			$this->dbg = new dbg($this);
		$this->unit = new unit(new dbg($class),$this);
	}
	public function __constructTest() {
		return true;
	}
	/**
	 * @getComments	true
	 * @depends	__constructTest
	 */
	public function getCommentTagsTest() {
		$test = $this->unit->getCommentTags($_SERVER['SCRIPT_FILENAME']);
		$isArray = $this->test(is_array($test),"getCommentTags did not return an array.");
		foreach($test["getCommentTagsTest"] AS $comment) {
			foreach($comment AS $tag=>$value) {
				if($tag=='@getComments' && $value==true)
					$valueFound = true;
			}
		}
		$valueFound = $this->test($valueFound, "@getComments was not found in unit->getCommentsTags array.");
		return ($isArray && $valueFound);
		
	}
	/**
	 * @depends	__constructTest
	 */
	public function randDataTest() {
		$data = array (
				"array" => dbg::randData('array'),
				"string" => dbg::randData('string'),
				"integer" => dbg::randData('integer'),
				"bool" => dbg::randData('bool'),
				"double" => dbg::randData('double')
		);
		$pass=NULL;
		foreach($data AS $type => $value) {
			$func = 'is_'.$type;
			//dbg::dump("$func($value): ".$func($value), false);
			//dbg::dump(gettype($value),false);
			if($pass==NULL)
				$pass = dbg::test($func($value), "$value is of type ".gettype($value));
			else
				$pass = $pass && dbg::test($func($value), "$value is of type ".gettype($value));
		}
		return $pass;
	}
	/**
	 * @depends	__constructTest
	 */
	public function msgTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function dumpTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function testTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function setNoCacheTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function getUnitTagsTest() {
		return true;
	}
	public function getTest() {
		return true;
	}
}

new dbgTest();