<?php
$categories = array(40, 22);
$number_of_posts_to_show = 4;
$default_thumbnail = "https://wordpress.uark.edu/business/files/2015/01/default-128x128.jpg";
$template = 'list.php';
$filename = 'feed.html';
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
    if ($category["ID"] == 22) {
      $featured = true;
    }
    foreach ($categories as $catid)
      if ($category["ID"] == $catid) {
        $news_item = true; break;
      }
    if($news_item) break;
  }
  if (!$news_item) continue;

  if(!empty($item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]))
    $thumbnail = str_replace("\\","",$item["featured_image"]["attachment_meta"]["sizes"]["thumbnail"]["url"]);
  else
    $thumbnail = $default_thumbnail;

  $item['title'] = htmlentities($item['title']);
  require(__DIR__."/../views/$template");

  if ($featured) $featured_items []= "$html_block";
  else $regular_items []= "$html_block";

}

$final_output = $html_opener;

foreach ($featured_items as $featured_item)
  $final_output .= "$featured_item";
foreach ($regular_items as $regular_item)
  $final_output .= "$regular_item";

$final_output .= $html_closer;

$somecontent = $final_output; 

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {

    // In our example we're opening $filename in write mode.
    if (!$handle = fopen($filename, 'w')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }

    echo "Success, wrote ($somecontent) to file ($filename)";

    fclose($handle);

} else {
    echo "The file $filename is not writable";
}
