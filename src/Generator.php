<?php
namespace razorbacks\walton\news\feed;

use InvalidArgumentException;

class Generator {
	protected $data;
	protected $categories;
	protected $number_of_posts_to_show;
	protected $default_thumbnail = "https://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg";
	protected $views;

	public function __construct($feed, $categories, $number_of_posts_to_show, $view){
		$this->views = __DIR__.'/../views';

		$feed = utf8_encode($feed);
		$this->data = json_decode($feed, true);
		if (!is_array($this->data)){
			throw new InvalidArgumentException(
				"JSON Error #".json_last_error().
				". see http://php.net/manual/en/function.json-last-error.php"
			);
		}
		if(empty($this->news[0]["link"])){
			throw new InvalidArgumentException(
				"Feed is empty"
			);
		}

		if(is_int($categories)){
			$this->categories[]= $categories;
		} else {
			if(!is_array($categories)){
				throw new InvalidArgumentException(
					"category IDs must be passed in an array"
				);
			}
			foreach($categories as $category){
				if(is_int($category) && $category > 0){
					$this->categories[]= $category;
				} else {
					throw new InvalidArgumentException(
						"category IDs must a positive integer"
					);
				}
			}
		}

		if(is_int($number_of_posts_to_show) && $number_of_posts_to_show > 0){
			$this->number_of_posts_to_show = $number_of_posts_to_show;
		} else {
			throw new InvalidArgumentException(
				"number of posts to show must a positive integer"
			);
		}

		if(!file_exists($this->views."/$view.php")){
			throw new InvalidArgumentException(
				"$view does not exist."
			);
		}
	}

	public function output($view){}
}
