<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Weather</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Weather live online.">
<meta property="og:title" content="Weather" />
<meta property="og:type" content="website" />
<meta property="og:description" content="Pogoda dla wybranych miast." />
<link rel="stylesheet" href="<?php echo site_url(); ?>css/bootstrap.min.css" type="text/css" />
<!--[if lt IE 10]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<style type="text/css">
	body { text-align: center; }
	.container { text-align: left; }
	table { margin-left: 25px; }
	</style>
<![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
	$('#button-submit').click(function(e) {
		e.preventDefault();

		var selvalue = $('select').val();
		$('#table').html('');

		if(selvalue == '') {
			$('#info').html('<div class="alert"><button class="close" data-dismiss="alert">×</button><strong>Uwaga:</strong> Wybierz miasto.</div>');
			$('.alert').delay(1000).fadeOut();
		} else {
			$('#button-submit').attr('disabled', 'disabled');
			$('#button-submit').html('<i class="icon-globe icon-white"></i> Sprawdzam...');

			//$.ajax can be used instead of $.post to set more options, ex. timeout
			$.post('<?php echo site_url(); ?>welcome/get_weather_soap', { weather_id: selvalue }, function(data) {
				if(data) {
					$('#table').html(data);
				} else {
					alert('Wystąpił błąd. Spróbuj ponownie.');
				}

				$('#button-submit').html('<i class="icon-globe icon-white"></i> Sprawdź pogodę');
				$('#button-submit').removeAttr('disabled');
			});
		}
		
	});
});
</script>
</head>
<body>

<div class="container">
	<div class="hero-unit">
		<h2>Sprawdź pogodę na świecie!</h2>
		<p>Na tej stronie możesz sprawdzić aktualną pogodę w wielu miejscach na świecie.</p>
	</div>
</div>