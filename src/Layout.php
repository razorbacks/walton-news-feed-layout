<?php
namespace razorbacks\walton\news;

use InvalidArgumentException;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Layout {
	protected $news;
	protected $categories;
	protected $number_of_posts_to_show;
	protected $default_thumbnail;
	protected $default_image;
	protected $html;

	public function __construct($feed, $categories, $count, $view){
		$this->default_thumbnail = getenv('NEWS_PUBLICATION_DEFAULT_THUMBNAIL');
		$this->default_image = getenv('NEWS_PUBLICATION_DEFAULT_IMAGE');

		$views = __DIR__."/../views";
		if(!file_exists("$views/$view.twig.html")){
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

		if(!is_array($categories)){
			$categories = array($categories);
		}

		foreach($categories as $category){
			$category = filter_var($category, FILTER_VALIDATE_INT);
			if(is_int($category) && $category > 0){
				$this->categories[]= $category;
			} else {
				throw new InvalidArgumentException(
					"category IDs must be a positive integer"
				);
			}
		}

		$count = filter_var($count, FILTER_VALIDATE_INT);
		if(is_int($count) && $count > 0){
			$this->number_of_posts_to_show = $count;
		} else {
			throw new InvalidArgumentException(
				"number of posts to show must a positive integer"
			);
		}

		$twig = new Twig_Environment(new Twig_Loader_Filesystem($views));
		$this->build($twig->loadTemplate("$view.twig.html"));
	}

	protected function build($template){
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

			if(!empty($item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"])){
				$item['thumbnail'] = str_replace("\\","",$item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]);
			} else {
				$item['thumbnail'] = $this->default_thumbnail;
			}

			if(!empty($item["featured_image"]["guid"])){
				$item['image'] = str_replace("\\","",$item["featured_image"]["guid"]);
			} else {
				$item['image'] = $this->default_image;
			}

			if ($featured){
				$featured_items []= $item;
			} else {
				$regular_items  []= $item;
			}
		}

		$output = array_merge($featured_items, $regular_items);

		$this->html = $template->render(array('output' => $output));
	}

	public function render(){
		return $this->html;
	}

	public function __toString(){
		return $this->render();
	}
}
