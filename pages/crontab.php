<table id="cronjobs">
	<thead>
		<tr>
			<th>Name</th>
			<th>Next Runtime</th>
			<th>Last Runtime</th>
			<th>Categories</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>

<?php
require_once __DIR__.'/../vendor/autoload.php';
use razorbacks\walton\news\Scheduler;

$scheduler = new Scheduler();

if(isset($_POST['categories'],$_POST['count'],$_POST['view'],$_POST['comments'])){
	$scheduler->createPublication($_POST);
}

if(isset($_POST['delete'])){
	$scheduler->deletePublication($_POST['delete']);
}

foreach($scheduler->getPublications() as $publication){
	echo "<tr>";

	$name = $publication->getComments();
	echo "<td>$name</td>";

	$next = $publication->getNextRuntime();
	echo "<td>$next</td>";

	$last = $publication->getLastRuntime();
	echo "<td>$last</td>";

	$categories = implode(',', $publication->categories);
	echo "<td>$categories</td>";

	$hash = $publication->getHash();
	?><td><form method="POST">
		<input type="hidden"  name="delete" value="<?=$hash?>"/>
		<button class="btn btn-danger">Delete</button>
	</form></td><?php

	echo "</tr>\n";
}

?>

	</tbody>
</table>

<style>
#cronjobs .sorting,
#cronjobs .sorting_asc,  #cronjobs .sorting_asc_disabled,
#cronjobs .sorting_desc, #cronjobs .sorting_desc_disabled {
	background-position: center left;
}
</style>

<script>
$("#cronjobs").DataTable();
</script>
