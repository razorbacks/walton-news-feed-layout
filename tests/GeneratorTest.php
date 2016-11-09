<?php
use razorbacks\walton\news\feed\Generator;

class GeneratorTest extends PHPUnit_Framework_TestCase {
	public function outputViewDataProvider(){
		return [
			'list' => ['list'],
		];
	}

	/**
	 * @dataProvider outputViewDataProvider
	 */
	public function testCanGenerateOutput($output){
		$json     = file_get_contents(__DIR__."/json/posts.json");
		$expected = file_get_contents(__DIR__."/html/$output.html");

		$generator = new Generator($json);
		$actual = $generator->output($output);

		$this->assertSame($expected, $actual);
	}
}
