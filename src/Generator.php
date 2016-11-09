<?php
namespace razorbacks\walton\news\feed;

use InvalidArgumentException;

class Generator {
	protected $data;

	public function __construct($json){
		$this->data = json_decode($json, true);
		if (!is_array($this->data)){
			throw new InvalidArgumentException(
				"JSON Error #".json_last_error().
				". see http://php.net/manual/en/function.json-last-error.php"
			);
		}
	}

	public function output($view){}
}
