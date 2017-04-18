<?php
use razorbacks\walton\news\Layout;

class LayoutTest extends PHPUnit_Framework_TestCase {
	public function testCanCreateListLayoutWithDefaultImages(){
		$json     = file_get_contents(__DIR__."/json/posts-no-image.json");
		$expected = file_get_contents(__DIR__."/html/list-no-image.html");

		$layout = new Layout($json, array(40, 22), 4, 'list');
		$actual = $layout->render();

		$this->assertSame((string)$layout, $actual);
		$this->assertSame($expected, $actual);
	}

	/**
	 * @testdox Can invalidate JSON
	 * @expectedException InvalidArgumentException
	 */
	public function testCanInvalidateJSON(){
		$invalid = file_get_contents(__DIR__.'/json/invalid.json');
		$generator = new Layout($invalid, 1, 1, 'list');
		$this->assertTrue(false);
	}
}
