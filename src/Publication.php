<?php
namespace razorbacks\walton\news;

use Crontab\Job;
use DateTime;
use Exception;
use InvalidArgumentException;

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

	protected function getQueryString(){
		$command = $this->getCommand();

		// break off query string
		if (strpos($command, '?') === false) {
			throw new Exception('No query string found.');
		}
		$pieces = explode('?', $command);
		$pieces = explode(' ', $pieces[1]);
		return $pieces[0];
	}

	protected function importCategories(){
		$query = $this->getQueryString();

		// get the categories
		if (strpos($query, 'categories') === false) {
			throw new Exception('No categories found.');
		}
		parse_str($query);
		$this->setCategories($categories);
	}

	public function getCategories(){
		if(empty($this->categories)){
			$this->importCategories();
		}
		return $this->categories;
	}

	public function setCategories($categories){
		if(!is_array($categories)){
			throw new InvalidArgumentException('Categories must be in array.');
		}
		foreach($categories as &$category){
			$category = filter_var($category, FILTER_VALIDATE_INT);
			if(!is_int($category)){
				throw new InvalidArgumentException("Category must be integer.");
			}
			if($category < 1){
				throw new InvalidArgumentException("Category must be positive. $category given.");
			}
		}
		sort($categories);
		$this->categories = $categories;
	}

	public function getPublicationFilename(){
		$categories = $this->getCategories();
		$categories = implode('-', $categories);
		return "$categories.php";
	}
}
