<?php

$default_thumbnail = "http://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg";
$feed = file_get_contents("https://wordpress.uark.edu/business/wp-json/posts"); 


$feed = utf8_encode($feed);
$news = json_decode($feed, true);
if(empty($news[0]["link"])) die();
?>

<div data-uark-news-widget-config="https://wordpress.uark.edu/business/uark-news-widget/new-homepage-feed/" class="uark-news-embed">
    <div class="col-md-12 news-item-oldschool">

<?php
foreach ($news as $item){
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
