<?php
namespace razorbacks\walton\news\feed;

use Crontab\Job;
use DateTime;
use Exception;

class Publication extends Job {
	protected $categories;

	public function getNextRuntime(){
		$date = new DateTime(date('h:i:s'));
		$dminute = (int)$date->format('i');
		$jminute = (int)$this->getMinute();

		// if minute passed, increment hour
		if($jminute <= $dminute){
			$date->modify("+1 hour");
		}

		$date->setTime((int)$date->format('h'), $jminute);
		return $date->format('h:i A');
	}

	protected function importCategories(){
		$command = $this->getCommand();

		// break off query string
		if (strpos($command, '?') === false) {
			throw new Exception('No query string found.');
		}
		$pieces = explode('?', $command);
		$query = $pieces[1];

		// get the categories
		if (strpos($query, 'categories') === false) {
			throw new Exception('No categories found.');
		}
		parse_str($query);
		$this->categories = $categories;
	}

	public function getCategories(){
		if(empty($this->categories)){
			$this->importCategories();
		}
		return $this->categories;
	}
}
