<?php

$number_of_posts_to_show = 4;
$default_thumbnail = "http://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg";
$feed = file_get_contents("https://wordpress.uark.edu/business/wp-json/posts"); 

$feed = utf8_encode($feed);
$news = json_decode($feed, true);
if(empty($news[0]["link"])) die("Feed empty.");

$featured_items = array();
$regular_items = array();
$count = 0;
foreach ($news as $item){

  if(++$count > $number_of_posts_to_show) break;

  $featured = false;
  $news_item = false;
  foreach ($item["terms"]["category"] as $category){
    if ($category["ID"] == "22") {
      $featured = true; break;
    } elseif ($category["ID"] == "40") {
      $news_item = true; break;
    }
  }
  if (!$featured && !$news_item) continue;

  if(!empty($item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]))
    $thumbnail = str_replace("\\","",$item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]);
  else
    $thumbnail = $default_thumbnail;
  
  $html_block = "
  <div class='media'>
    <a class='pull-left' href='$item[link]'>
      <img height='128px' src='$thumbnail' alt='$item[title] featured image'/>
    </a>
    <div class='media-body'>
      <h4 class='media-heading'><a href='$item[link]'>$item[title]</a></h4>
      <p>$item[excerpt]</p>
    </div>
  </div>
  ";
  
  if ($featured) $featured_items []= "$html_block";
  else $regular_items []= "$html_block";

}
?>

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>

<div data-uark-news-widget-config="https://wordpress.uark.edu/business/uark-news-widget/new-homepage-feed/" class="uark-news-embed">
    <div class="col-md-12 news-item-oldschool">

<?php
  foreach ($featured_items as $featured_item)
    echo "$featured_item";
  foreach ($regular_items as $regular_item)
    echo "$regular_item";
?>

    </div>
</div>
