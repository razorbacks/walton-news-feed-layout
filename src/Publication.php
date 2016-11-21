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
	protected $valid = true;

	public function __construct($array, $minute){
		$this->initialize($array, $minute);
	}

	public function setCommand($command){
		// escape % for crontab
		// https://github.com/yzalis/Crontab/issues/34
		// http://www.ducea.com/2008/11/12/using-the-character-in-crontab-entries/
		return parent::setCommand(str_replace('%', '\\\\%', $command));
	}

	public function getCommand(){
		return str_replace('\\%', '%', parent::getCommand());
	}

	public function getNextRuntime(){
		if(!$this->valid){
			return false;
		}

		$date = new DateTime(date('H:i:s'));
		$dminute = (int)$date->format('i');
		$jminute = (int)$this->getMinute();

		// if minute passed, increment hour
		if($jminute <= $dminute){
			$date->modify("+1 hour");
		}

		$date->setTime((int)$date->format('H'), $jminute);
		return $date->format('h:i A');
	}

	public function getLastRuntime(){
		$filename = $this->getPublicationFilename();
		$modified = filemtime($filename);
		if($modified === false){
			return "File doesn't exist: $filename";
		}

		$datetime = new DateTime;
		$datetime->setTimestamp($modified);
		return $datetime->format('Y-m-d H:i - h:i A l, F d, Y');
	}

	public function importQueryString(){
		$command = $this->getCommand();

		$pieces = explode(' ', $command);
		if(isset($pieces[2])){
			$query = trim($pieces[2],"'");
			parse_str($query, $array);

			// get categories, count, and view
			foreach(array('categories', 'count', 'view') as $property){
				if(!isset($array[$property])){
					$this->valid = false;
					break;
				}
				// use custom setters with variable variables from parse_str
				$set = "set$property";
				$this->$set($array[$property]);
			}
		} else {
			$this->valid = false;
		}
	}

	public function __get($property){
		if(!$this->valid){
			return false;
		}

		$properties = array('valid', 'categories', 'count', 'view');
		if(!in_array($property, $properties, true)){
			$properties = implode(', ', $properties);
			throw new InvalidArgumentException("Only $properties are accessible");
		}
		if(empty($this->$property)){
			$this->importQueryString();
		}
		if(empty($this->$property)){
			return false;
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
		return $this;
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
		return $this;
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
		return $this;
	}

	public function getPublicationFilename(){
		if(!$this->valid){
			return false;
		}

		$view  = $this->__get('view');
		$count = $this->__get('count');
		$categories = $this->__get('categories');
		$categories = implode('-', $categories);

		$directory  = realpath(__DIR__."/../publications");
		return "$directory/$view.$count.$categories.html";
	}

	public function getIncludeScript(){
		return
			"<?php include '".
			$this->getPublicationFilename().
			"'; /* ".
			$this->getComments().
			" */ ?>";
	}

	protected function buildQueryString(){
		$data = array(
			'categories' => $this->__get('categories'),
			'count'      => $this->__get('count'),
			'view'       => $this->__get('view'),
		);
		return http_build_query($data);
	}

	public function initialize($array, $minute){
		$this
			->setCategories($array['categories'])
			->setCount($array['count'])
			->setView($array['view'])
			->setMinute($minute)
			->setHour('*')
			->setDayOfMonth('*')
			->setMonth('*')
			->setDayOfWeek('*')
			->setCommand(
				'/usr/bin/php ' .
				realpath(__DIR__.'/../pages/getlayout.php') .
				" '" . $this->buildQueryString() . "'" .
				" '" . $this->getPublicationFilename() . "'" .
				" > /dev/null 2>&1"
			)
			->setComments($array['comments'])
		;
	}
}
