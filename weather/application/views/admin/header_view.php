<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Weather admin</title>
<link rel="stylesheet" href="<?php echo site_url('css/admin.css'); ?>" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Robots" content="noindex, nofollow" />
</head>
<body>

<div id="top">
	<div class="content">
		<a href="<?php echo site_url('admin/cities'); ?>"></a>
		
		<div class="clear"></div>
		
		<div id="menu">
			<ul>
				<li><a href="<?php echo site_url('admin/cities'); ?>" style="margin-left: 0;" <?php if(isset($menu) && $menu == 'cities') echo ' class="visited"'; ?>>Miasta</a></li>
				<li><a href="<?php echo site_url('admin/config'); ?>"<?php if(isset($menu) && $menu == 'config') echo ' class="visited"'; ?>>Ustawienia</a></li>
			</ul>
		</div>

		<div class="clear"></div>
	</div>
</div>

<div class="clear"></div>

<div id="page">
