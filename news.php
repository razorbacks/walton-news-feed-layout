<?php

$default_thumbnail = "http://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg";
$feed = file_get_contents("https://wordpress.uark.edu/business/wp-json/posts"); 


$feed = utf8_encode($feed);
$news = json_decode($feed, true);
if(empty($news[0]["link"])) die();
?>

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>

<div data-uark-news-widget-config="https://wordpress.uark.edu/business/uark-news-widget/new-homepage-feed/" class="uark-news-embed">
    <div class="col-md-12 news-item-oldschool">

<?php
$featured_items = array();
$regular_items = array();
$html_block = "";
foreach ($news as $item){
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
?>

  <div class="media">
    <a class="pull-left" href="<?php echo $item["link"]; ?>">
      <?php
	if(!empty($item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]))
	  $thumbnail = str_replace("\\","",$item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]);
	else
	  $thumbnail = $default_thumbnail;
      ?>
      <img height="128px" src="<?php echo "$thumbnail"; ?>" alt="<?php echo $item["title"]; ?> featured image"/>
    </a>
    <div class="media-body">
      <h4 class="media-heading"><a href="<?php echo $item["link"]; ?>"><?php echo $item["title"]; ?></a></h4>
      <p><?php echo $item["excerpt"]; ?></p>
    </div>
  </div>

<?php
}
?>

    </div>
</div>
