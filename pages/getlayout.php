<?php

use razorbacks\walton\news\Layout;

if(isset($argv[1])){
	parse_str($argv[1], $_GET);
}

if(!isset($_GET['categories'],$_GET['count'],$_GET['view'])){
	echo "categories, count, and view required.";
} else {
	require_once __DIR__.'/../vendor/autoload.php';
	$feed = file_get_contents("https://wordpress.uark.edu/business/wp-json/posts");
	$layout = new Layout($feed, $_GET['categories'],$_GET['count'],$_GET['view']);
	echo $layout->render();
}
