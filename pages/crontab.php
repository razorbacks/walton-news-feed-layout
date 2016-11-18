<table id="cronjobs">
	<thead>
		<tr><th>Name</th><th>Next Runtime</th><th>Categories</th></tr>
	</thead>
	<tbody>

<?php
require_once __DIR__.'/../vendor/autoload.php';
use razorbacks\walton\news\Scheduler;

$scheduler = new Scheduler();

if(isset($_POST['categories'],$_POST['count'],$_POST['view'],$_POST['comments'])){
	$scheduler->createPublication($_POST);
}

foreach($scheduler->getPublications() as $publication){
	echo "<tr>";

	$name = $publication->getComments();
	echo "<td>$name</td>";

	$time = $publication->getNextRuntime();
	echo "<td>$time</td>";

	$categories = implode(',', $publication->categories);
	echo "<td>$categories</td>";

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
