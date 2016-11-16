<?php
namespace razorbacks\walton\news;

use Crontab\Job;
use DateTime;
use Exception;
use InvalidArgumentException;

class Publication extends Job {
	protected $categories;
	protected $count;
	protected $view;

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

	protected function importQueryString(){
		$command = $this->getCommand();

		// break off query string
		if (strpos($command, '?') === false) {
			throw new Exception('No query string found.');
		}
		$pieces = explode('?', $command);
		$pieces = explode(' ', $pieces[1]);
		$query = $pieces[0];
		parse_str($query);

		// get categories, count, and view
		foreach(array('categories', 'count', 'view') as $property){
			if (strpos($query, $property) === false) {
				throw new Exception("No $property found in query string.");
			}
			// use custom setters with variable variables from parse_str
			$set = "set$property";
			$this->$set($$property);
		}
	}

	public function __get($property){
		$properties = array('categories', 'count', 'view');
		if(!in_array($property, $properties, true)){
			$properties = implode(', ', $properties);
			throw new InvalidArgumentException("Only $properties are accessible");
		}
		if(empty($this->$property)){
			$this->importQueryString();
		}
		return $this->$property;
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

	public function setCount($count){
		$count = filter_var($count, FILTER_VALIDATE_INT);
		if(!is_int($count)){
			throw new InvalidArgumentException("Count must be integer.");
		}
		if($count < 1){
			throw new InvalidArgumentException("Count must be positive. $count given.");
		}
		$this->count = $count;
	}

	public function setView($view){
		if(!is_string($view)){
			throw new InvalidArgumentException("View name must be a string.");
		}

		$views = __DIR__."/../views";
		if(!file_exists("$views/$view.twig.html")){
			throw new InvalidArgumentException(
				"$view does not exist."
			);
		}

		$this->view = $view;
	}

	public function getPublicationFilename(){
		$view  = $this->__get('view');
		$count = $this->__get('count');
		$categories = $this->__get('categories');
		$categories = implode('-', $categories);
		return __DIR__."/../publications/$view.$count.$categories.php";
	}
}
