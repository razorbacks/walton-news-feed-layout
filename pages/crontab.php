<table id="cronjobs">
	<thead>
		<tr><th>Name</th><th>Next Runtime</th><th>Command</th></tr>
	</thead>
	<tbody>
<?php

require_once __DIR__.'/../vendor/autoload.php';
use Crontab\Crontab;
use Crontab\Job;

$crontab = new Crontab();

$jobs = $crontab->getJobs();

function getNextRuntime(Job $job){
	$date = new DateTime(date('h:i:s'));
	$dminute = (int)$date->format('i');
	$jminute = (int)$job->getMinute();

	// if minute passed, increment hour
	if($jminute <= $dminute){
		$date->modify("+1 hour");
	}

	$date->setTime((int)$date->format('h'), $jminute);
	return $date->format('h:i A');
}

foreach($jobs as $job){
	echo "<tr>";

	$name = $job->getComments();
	echo "<td>$name</td>";

	$time = getNextRuntime($job);
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
