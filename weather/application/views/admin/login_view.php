<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Weather admin</title>
<link rel="stylesheet" href="<?php echo site_url('css/admin.css'); ?>" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Robots" content="noindex, nofollow" />
</head>
<body>

<div class="content">

	<div id="login_box">
		<div class="small_box_top"></div>
		
		<div class="small_box_content">
			<?php 
	
				if($this->session->flashdata('err'))
					echo '<span class="red">'.$this->session->flashdata('err');
				
				if($this->session->flashdata('msg'))
					echo '<span class="green">'.$this->session->flashdata('msg');
	
			?>
			<form action="<?php echo site_url('admin/login'); ?>" method="post">
				<label for="login" class="label">Login</label><input type="text" name="login" id="login" class="login_input" /><br clear="all" />
				<label for="password" class="label">Hasło</label><input type="password" name="password" id="password" class="login_input" /><br clear="all" />
				<input type="submit" value="Zaloguj" class="login_submit" />
				<div class="clear"></div>
			</form>
		</div>
		
		<div class="small_box_bottom"></div>
	</div>
</div>

</body>
</html>