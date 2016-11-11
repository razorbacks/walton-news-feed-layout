<script src="http://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<div class="container">
	<h2>Create</h2>
	<form class="form-horizontal">
		<div class="form-group">
			<label for="categories" class="control-label col-sm-2">Categories:</label>
			<div class="col-sm-10">
				<?php require __DIR__.'/../views/categories.select.html'; ?>
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
					<option value="list">List</option>
				</select>
			</div>
		</div>

		<div class="col-sm-offset-2 col-sm-10 well">
			<h3>Preview</h3>
			<div class="row">
				<div class="col-sm-4">
					<p><a href="college-interior.php" class="btn btn-default" target="_blank">College Interior</a></p>
				</div>
				<div class="col-sm-4">
					<p><a href="full-width-interior-page.php" class="btn btn-default" target="_blank">Full-Width Interior</a></p>
				</div>
				<div class="col-sm-4">
					<p><a href="two-column-interior-page.php" class="btn btn-default" target="_blank">Two-Column Interior</a></p>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</div>
	</form>
</div>

<style>
.select2-container[style] {
	width: 100% !important;
}
</style>
