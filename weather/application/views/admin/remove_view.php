<div id="main">
	<h1>Miasto - usuń</h1>
	
	<p>
		Czy na pewno usunąć miasto: <?php echo $city->city.', '.$city->location; ?>?
	</p>

	<form action="<?php echo site_url('admin/remove'); ?>" method="post">
		<input type="hidden" name="weather_id" value="<?php echo $city->weather_id; ?>">
		<input type="hidden" name="delete" value="0">
		<input type="submit" value="Nie" style="color: red;">
	</form>
	
	<br />
	
	<form action="<?php echo site_url('admin/remove'); ?>" method="post">
		<input type="hidden" name="weather_id" value="<?php echo $city->weather_id; ?>">
		<input type="hidden" name="delete" value="1">
		<input type="submit" value="Tak" style="color: green;">
	</form>
	
</div>

<div class="clear"></div>