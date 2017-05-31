<table id="cronjobs">
	<thead>
		<tr>
			<th>Name</th>
			<th>OU Asset Code</th>
			<th>Next Runtime</th>
			<th>Run Now</th>
			<th>Last Runtime</th>
			<th>Filename</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>

<?php
require_once __DIR__.'/../vendor/autoload.php';
use razorbacks\walton\news\Scheduler;
use razorbacks\walton\news\Xsrf;

$scheduler = new Scheduler();

// create
if(isset($_POST['categories'],$_POST['count'],$_POST['view'],$_POST['comments'])){
	Xsrf::verify();
	$scheduler->createPublication($_POST);
}

// delete
if(isset($_POST['delete'])){
	Xsrf::verify();
	$scheduler->deletePublication($_POST['delete']);
}

// run
if(isset($_POST['run'])){
	Xsrf::verify();
	$scheduler->runPublication($_POST['run']);
}

foreach($scheduler->getPublications() as $publication){
	echo "<tr>";

	$name = $publication->getComments();
	echo "<td>$name</td>";

	$incode = $publication->getIncludeScript();
	?><td><button data-incode="<?php echo $incode; ?>" class="btn btn-primary btn-incode">Show Code</button></td><?php

	$next = $publication->getNextRuntime();
	echo "<td>$next</td>";

	$hash = $publication->getHash();
	?><td><form method="POST">
		<input name="xsrf" type="hidden" value="<?php echo $xsrf;?>">
		<input type="hidden" name="run" value="<?php echo $hash; ?>"/>
		<button class="btn btn-warning">Publish Now</button>
	</form></td><?php

	$last = $publication->getLastRuntime();
	echo "<td>$last</td>";

	$filename = basename($publication->getPublicationFilename());
	echo "<td>$filename</td>";

	$hash = $publication->getHash();
	?><td><form method="POST">
		<input name="xsrf" type="hidden" value="<?php echo $xsrf;?>">
		<input type="hidden" name="delete" value="<?php echo $hash; ?>"/>
		<button class="btn btn-danger">Delete</button>
	</form></td><?php

	echo "</tr>\n";
}
?>

	</tbody>
</table>

<div style="display:none" id="dialog" title="OmniUpdate Asset Code">
	<pre id="incode"></pre>
</div>

<style>
#cronjobs .sorting,
#cronjobs .sorting_asc,  #cronjobs .sorting_asc_disabled,
#cronjobs .sorting_desc, #cronjobs .sorting_desc_disabled {
	background-position: center left;
}
</style>

<script>
$( "#dialog" ).dialog({
	autoOpen: false,
	width: 850,
	show: {
		effect: "fade",
		duration: 100
	},
	hide: {
		effect: "fade",
		duration: 100
	}
});
$(".btn-incode").click(function(){
	$("#incode").text($(this).data("incode"));
	$("#dialog").dialog("open");
});
$("#cronjobs").DataTable();
</script>
