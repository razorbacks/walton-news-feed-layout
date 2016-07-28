<?php
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
