<?php
namespace razorbacks\walton\news;

use InvalidArgumentException;
use Exception;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Layout {
	protected $news;
	protected $categories;
	protected $number_of_posts_to_show;
	protected $html;

	public function __construct($feed, $categories, $count, $view){
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
		if ( empty($this->news) ) {
			throw new InvalidArgumentException(
				"Feed is empty."
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

		$twig = new Twig_Environment(
			new Twig_Loader_Filesystem($views),
			array('autoescape' => false)
		);
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

			// canonicalize rendered item attributes
			$item['title'] = $item['title']['rendered'];
			$item['excerpt'] = $item['excerpt']['rendered'];

			// images
			if (!empty($item['_links']['wp:featuredmedia'][0]['href'])) {
				$url = $item['_links']['wp:featuredmedia'][0]['href'];
				$item['image'] = $this->fetchImageUrls($url);
			} else {
				$item['image'] = array();
			}
			$item['image'] = $this->getDefaultImages($item['image']);

			// show featured items first
			$featured = false;
			foreach ($item["categories"] as $category){
				if ($category == getenv('NEWS_PUBLICATION_FEATURED_CATEGORY_ID')) {
					$featured = true;
					break;
				}
			}
			if ($featured){
				$featured_items []= $item;
			} else {
				$regular_items  []= $item;
			}
		}

		$items = array_merge($featured_items, $regular_items);
		$this->html = $template->render(array('items' => $items));
	}

	/**
	 * fetch the URLs for a post's featured image and thumbnail
	 *
	 * @param  string $url wp-json post media endpoint
	 * @return array  contains image URLs
	 */
	protected function fetchImageUrls($url)
	{
		$json = file_get_contents($url);
		if ( empty($json) ) {
			throw new Exception("Nothing returned from URL: $url");
		}

		$media = json_decode($json, $array = true);
		if ( !is_array($media) ) {
			throw new Exception(
				"JSON Error #".json_last_error().
				". see http://php.net/manual/en/function.json-last-error.php"
			);
		}

		$array = array();

		foreach ( $media['media_details']['sizes'] as $size => $image ) {
			$array[$size] = $image['source_url'];
		}

		return $array;
	}

	/**
	 * fills any missing image sizes with defaults
	 *
	 * @param  array $images
	 * @return array
	 */
	public function getDefaultImages($images)
	{
		$sizes = array(
			'thumbnail',
			'medium',
			'medium_large',
			'large',
			'full',
		);

		foreach ( $sizes as $size ) {
			if ( empty($images[$size]) ) {
				$env = strtoupper("NEWS_PUBLICATION_DEFAULT_IMAGE_$size");
				$images[$size] = getenv($env);
			}
		}

		return $images;
	}

	public function render(){
		return $this->html;
	}

	public function __toString(){
		return $this->render();
	}
}
