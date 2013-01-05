<?php 
	if($this->session->flashdata('err'))
		echo '<p class="red">'.$this->session->flashdata('err').'</p>';
		
	if($this->session->flashdata('msg'))
		echo '<p class="green">'.$this->session->flashdata('msg').'</p>';
?>

<form action="<?php echo site_url(); ?>admin/cities_check" method="post">
<label for="city" class="label2">Podaj nazwę miasta</label>
<input type="text" name="city" id="city" class="input_middle">
<input type="submit" value="Szukaj">
</form>

<?php if($location): ?>
<form action="<?php echo site_url(); ?>admin/city_add" method="post">
<label class="label2">Nazwa miasta: <?php echo $this->session->flashdata('city'); ?></label>
<input type="hidden" name="city" value="<?php echo $this->session->flashdata('city'); ?>"><br clear="all">
<label class="label2">Nazwa znaleziona: <?php echo $location; ?></label>
<input type="hidden" name="location" value="<?php echo $location; ?>"><br clear="all">
<input type="submit" value="Zapisz to miasto w bazie">

</form>
<?php endif; ?>

<?php if($cities): ?>
<table class="table">
	<tr>
		<td><strong>Nazwa</strong></td>
		<td><strong>Lokalizacja</strong></td>
		<td></td>
		<td></td>
	</tr>
<?php foreach($cities as $city): $i++; if($i%2) $class = ' class="dark"'; else $class = ''; ?>
	<tr>
		<td<?php echo $class; ?>><?php echo $city->city; ?></td>
		<td<?php echo $class; ?>><?php echo $city->location; ?></td>
		<td<?php echo $class; ?>><a href="<?php echo site_url().'admin/edit/'.$city->weather_id; ?>">Edytuj</a></td>
		<td<?php echo $class; ?>><a href="<?php echo site_url().'admin/confirm_remove/'.$city->weather_id; ?>">Usuń</a></td>
	</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

