<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<select id="select-categories" name="categories[]" multiple="multiple">
<?php
$dom = new DOMDocument;
$dom->loadHTMLFile(__DIR__.'/cats.html');

foreach($dom->getElementsByTagName('li') as $item){
	// id is in the class
	$id = $item->getAttribute('class');

	// filter out non-numeric
	// http://stackoverflow.com/a/35619532/4233593
	$id = preg_replace('/\D/', '', $id);

	// get the category name text
	$category = trim($item->textContent);

	// print option
	echo "\t<option value='$id'>$category</option>\n";
}
?>
</select>

<script type="text/javascript">
$("#select-categories").select2();
</script>
