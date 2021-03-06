<?php

session_start();

$xsrf = substr(str_shuffle(MD5(microtime())), 0, 32);

require_once __DIR__.'/vendor/autoload.php';

use jpuck\Error\Handler;
use razorbacks\walton\news\Backup;

Handler::convertErrorsToExceptions();
Handler::swift();
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>News Feed Layout Scheduler</title>
	<meta name="description" content="This application will help you create a news feed layout and schedule it.">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>

	<!-- Bootstrap -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<!-- jQuery UI -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<!-- Datatables -->
	<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
</head>

<body>
<div class="container">

	<h2>Create</h2>
	<form id="layout-form" class="form-horizontal">
		<input name="xsrf" type="hidden" value="<?php echo $xsrf;?>">
		<div class="form-group">
			<label for="comments" class="control-label col-sm-2">Name:</label>
			<div class="col-sm-10">
				<input type="text" name="comments" required class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="categories" class="control-label col-sm-2">Categories:</label>
			<div class="col-sm-10">
				<?php require __DIR__.'/pages/categories.select.html'; ?>
			</div>
		</div>

		<div class="form-group">
			<label for="count" class="control-label col-sm-2">Count:</label>
			<div class="col-sm-10">
				<input name="count" type="number" min="1" required class="form-control"/>
			</div>
		</div>

		<div class="form-group">
			<label for="view" class="control-label col-sm-2">Layout:</label>
			<div class="col-sm-10">
				<select name="view" required class="form-control">
					<option value="tile">Tile</option>
					<option value="list">List</option>
				</select>
			</div>
		</div>

		<div class="col-sm-offset-2 col-sm-10 well">
			<h3>Preview</h3>
			<div class="row">
				<div class="col-sm-4">
					<p><button type="submit" formmethod="GET" formaction="pages/previews/two-column-interior-page.php" class="btn btn-info btn-preview">Two-Column Interior</button></p>
				</div>
				<div class="col-sm-4">
					<p><button type="submit" formmethod="GET" formaction="pages/previews/full-width-interior-page.php" class="btn btn-info btn-preview">Full-Width Interior</button></p>
				</div>
				<div class="col-sm-4">
					<p><button type="submit" formmethod="GET" formaction="pages/previews/college-interior.php" class="btn btn-info btn-preview">College Interior</button></p>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button id="btn-submit" type="submit" formmethod="POST" class="btn btn-success">Schedule Publication</button>
			</div>
		</div>
	</form>

	<h2>View</h2>
	<?php require __DIR__.'/pages/crontab.php'; ?>

	<h2>Backups</h2>
	<?php
	$backup = new Backup($scheduler);
	echo $backup->renderFileList();
	?>

</div>

<style>
.select2-container[style] {
	width: 100% !important;
}
h2 {
	border-bottom: 1px solid black;
}
</style>

<script>
$(".btn-preview").click(function(){
	$('#layout-form').attr('target', '_blank');
});
$("#btn-submit").click(function(){
	$('#layout-form').attr('target', '');
});
</script>
</body>
</html>

<?php
$_SESSION["xsrf"] = $xsrf;
