<?php
use razorbacks\walton\news\feed\Generator;

class ListTest extends PHPUnit_Framework_TestCase {
	public function testCanGenerateList(){
		$generator = new Generator;
		$this->assertTrue($generator instanceof Generator);
	}
}
