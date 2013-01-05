<div class="container">
	<div class="row">
		<div class="span12">
			<div class="control-group">
				<form action="#" method="post" class="well form-horizontal" id="form">
					<div id="info"></div>
				
					<label class="control-label" for="weather_id">Wybierz miasto</label>
					<div class="controls">
						<select id="weather_id">
							<option value="">Wybierz</option>
							<?php if($cities): ?>
							<?php foreach($cities as $city): ?>
							<option value="<?php echo $city->weather_id; ?>"><?php echo $city->city; ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
						
						<button class="btn btn-success" id="button-submit" data-loading-text="loading..."><i class="icon-globe icon-white"></i> Sprawdź pogodę</button>
					</div>
				</form>
				
				<div id="table"></div>
			</div>
		</div>
	</div>
</div>