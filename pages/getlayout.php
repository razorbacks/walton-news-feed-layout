<?php
require_once __DIR__.'/../vendor/autoload.php';

use jpuck\Error\Handler;
use Dotenv\Dotenv;
use razorbacks\walton\news\Layout;

Handler::convertErrorsToExceptions();
Handler::swift();

if(isset($argv[1])){
	parse_str($argv[1], $_GET);
}

if(!isset($_GET['categories'],$_GET['count'],$_GET['view'])){
	echo "categories, count, and view required.";
} else {
	$dotenv = new Dotenv(dirname(__DIR__));
	$dotenv->load();

	$endpoint = getenv('NEWS_PUBLICATION_ENDPOINT');
	if ( empty($endpoint) ) {
		throw new Exception("NEWS_PUBLICATION_ENDPOINT cannot be empty.");
	}

	$query = array(
		'categories' => $_GET['categories'],
		'per_page' => $_GET['count'],
	);
	$query = http_build_query($query);
	$feed = file_get_contents("$endpoint?$query");

	$layout = new Layout($feed, $_GET['categories'],$_GET['count'],$_GET['view']);

	if(isset($argv[2])){
		file_put_contents($argv[2], $layout->render());
	} else {
		echo $layout->render();
	}
}
