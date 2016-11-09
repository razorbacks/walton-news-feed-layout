<?php
namespace razorbacks\walton\news\feed;

use InvalidArgumentException;

class Generator {
	protected $data = [];

	public function __construct($json){
		$this->data = json_decode($json, true);
		if (!is_array($this->data)){
			throw new InvalidArgumentException(
				"Bad JSON: ".json_last_error_msg()
			);
		}
	}

	public function output($view){}
}
