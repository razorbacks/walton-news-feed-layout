<?php
$html_opener = "
<div data-uark-news-widget-config='https://wordpress.uark.edu/business/uark-news-widget/new-homepage-feed/' class='uark-news-embed'>
    <div class='col-md-12 news-item-oldschool'>
";

$html_block = "
<div class='media'>
<a class='pull-left' href='$item[link]'>
    <img src='$thumbnail' alt='$item[title] featured image'/>
</a>
<div class='media-body'>
    <h4 class='media-heading'><a href='$item[link]'>$item[title]</a></h4>
    $item[excerpt]
</div>
</div>
";

$html_closer = "
    </div>
</div>
<style>
@media only screen and (min-width: 480px){
  .media img {
      width: 128px;
  }
}
</style>
";
