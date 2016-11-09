<?php
namespace razorbacks\walton\news\feed;

use InvalidArgumentException;

class Layout {
	protected $data;
	protected $categories;
	protected $number_of_posts_to_show;
	protected $default_thumbnail = "https://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg";
	protected $view;
	protected $html;

	public function __construct($feed, $categories, $count, $view){
		$this->view = __DIR__."/../views/$view.php";
		if(!file_exists($this->view)){
			throw new InvalidArgumentException(
				"$view does not exist."
			);
		}

		$feed = utf8_encode($feed);
		$this->news = json_decode($feed, true);
		if (!is_array($this->news)){
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

		if(is_int($count) && $count > 0){
			$this->number_of_posts_to_show = $count;
		} else {
			throw new InvalidArgumentException(
				"number of posts to show must a positive integer"
			);
		}

		$this->build();
	}

	protected function build(){
		$featured_items = array();
		$regular_items = array();
		$count = 0;
		foreach ($this->news as $item){
			if(++$count > $this->number_of_posts_to_show){
				break;
			}

			$featured = false;
			$news_item = false;
			foreach ($item["terms"]["category"] as $category){
				if ($category["ID"] == 22) {
					$featured = true;
				}
				foreach ($this->categories as $catid){
					if ($category["ID"] == $catid) {
						$news_item = true; break;
					}
				}
				if($news_item){
					break;
				}
			}
			if (!$news_item){
				continue;
			}

			if(!empty($item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]))
				$thumbnail = str_replace("\\","",$item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]);
			else
				$thumbnail = $this->default_thumbnail;

			$item['title'] = htmlentities($item['title']);
			require($this->view);

			if ($featured){
				$featured_items []= "$html_block";
			} else {
				$regular_items []= "$html_block";
			}
		}

		$final_output = $html_opener;

		foreach ($featured_items as $featured_item){
			$final_output .= "$featured_item";
		}
		foreach ($regular_items as $regular_item){
			$final_output .= "$regular_item";
		}

		$this->html = $final_output.$html_closer;
	}

	public function __toString(){
		return $this->html;
	}
}
