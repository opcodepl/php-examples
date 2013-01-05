
	<h1>Miasto - edycja</h1>
	
	<?php 
		if($this->session->flashdata('err'))
			echo '<p class="red">'.$this->session->flashdata('err').'</p>';
		
		if($this->session->flashdata('msg'))
			echo '<p class="green">'.$this->session->flashdata('msg').'</p>';
	?> 
	
	<p>
		Edycja wyświetlanej nazwy miasta "<?php echo $city->city.'", '.$city->location; ?>:
	</p>
	
	<form action="<?php echo site_url(); ?>admin/save" method="post">
		<label for="city" class="label2" for="city">Nazwa</label>
		<input class="input_medium" type="text" name="city" id="city" value="<?php echo $this->session->flashdata('city') ? $this->session->flashdata('city') : $city->city; ?>" maxlength="250">
		<input type="hidden" name="weather_id" value="<?php echo $city->weather_id; ?>">
		<input type="submit" value="Zapisz">
	</form>
		
		<p>
			<a href="<?php echo site_url(); ?>admin/cities">&laquo; powrót</a>
		</p>	
