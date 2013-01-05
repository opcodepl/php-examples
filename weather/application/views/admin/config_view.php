
	<h1>Konfiguracja</h1>
	
	<?php 
		if($this->session->flashdata('err'))
			echo '<p class="red">'.$this->session->flashdata('err').'</p>';
		
		if($this->session->flashdata('msg'))
			echo '<p class="green">'.$this->session->flashdata('msg').'</p>';
	?> 
	
	<form action="<?php echo site_url(); ?>admin/save_config" method="post">
			<label for="soap_source" class="label2 w">Adres usługi sieciowej</label>
			<input type="text" class="input_long" name="soap_source" id="soap_source" value="<?php echo $this->session->flashdata('soap_source') ? $this->session->flashdata('soap_source') : $config->soap_source; ?>" maxlength="250"><br clear="all">
			<label for="timeout" class="label2 w">Timeout (w sek.)</label>
			<input type="text" class="input_short" name="timeout" id="timeout" value="<?php echo $this->session->flashdata('timeout') ? $this->session->flashdata('timeout') : $config->timeout; ?>" maxlength="3"><br clear="all">
			<input type="submit" value="Zapisz">
		</form>
	</div>
