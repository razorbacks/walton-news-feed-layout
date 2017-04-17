<?php

use razorbacks\walton\news\Layout;

if(isset($argv[1])){
	parse_str($argv[1], $_GET);
}

if(!isset($_GET['categories'],$_GET['count'],$_GET['view'])){
	echo "categories, count, and view required.";
} else {
	require_once __DIR__.'/../vendor/autoload.php';

	$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
	$dotenv->load();

	$endpoint = getenv('NEWS_PUBLICATION_ENDPOINT');
	if ( empty($endpoint) ) {
		throw new Exception("NEWS_PUBLICATION_ENDPOINT cannot be empty.");
	}

	$filter = array(
		'filter' => array(
			'cat' => implode(',', $_GET['categories']),
			'posts_per_page' => $_GET['count'],
		)
	);
	$filter = http_build_query($filter);
	$feed = file_get_contents("$endpoint?$filter");

	$layout = new Layout($feed, $_GET['categories'],$_GET['count'],$_GET['view']);

	if(isset($argv[2])){
		file_put_contents($argv[2], $layout->render());
	} else {
		echo $layout->render();
	}
}
