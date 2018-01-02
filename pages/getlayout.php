<?php
require_once __DIR__.'/../vendor/autoload.php';

use jpuck\Error\Handler;
use Dotenv\Dotenv;
use razorbacks\walton\news\Layout;
use razorbacks\walton\news\Request;

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

	// new wordpress server rejects missing user agent string
	$userAgent = Request::USERAGENT;
	$opts = [
	    "http" => [
	        "method" => "GET",
	        "header" => "User-Agent: $userAgent\r\n"
	    ]
	];
	$context = stream_context_create($opts);

	$feed = file_get_contents("$endpoint?$query", false, $context);

	$layout = new Layout($feed, $_GET['categories'],$_GET['count'],$_GET['view']);

	if(isset($argv[2])){
		file_put_contents($argv[2], $layout->render());
	} else {
		echo $layout->render();
	}
}
