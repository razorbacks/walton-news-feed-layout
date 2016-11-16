<table id="cronjobs">
	<thead>
		<tr><th>Name</th><th>Next Runtime</th><th>Command</th></tr>
	</thead>
	<tbody>

<?php
require_once __DIR__.'/../vendor/autoload.php';
use razorbacks\walton\news\feed\Scheduler;

$scheduler = new Scheduler();



foreach($scheduler->getPublications() as $job){
	echo "<tr>";

	$name = $job->getComments();
	echo "<td>$name</td>";

	$time = $job->getNextRuntime();
	echo "<td>$time</td>";

	$command = $job->getCommand();
	echo "<td>$command</td>";

	echo "</tr>\n";
}

?>

	</tbody>
</table>

<script>
$("#cronjobs").DataTable();
</script>
