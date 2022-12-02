<!doctype html>
<html>
	<head>
		<title>Los Cabos Anchor</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'" />';
		}
		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo">
    				<a href="https://www.caboanchor.com" target="_blank" rel="nofollow">Los Cabos Anchor Admin</a>
					<!--<span>v<?php echo PJ_SCRIPT_VERSION;?></span>-->
    			</div>
			</div>
			<div id="middle">
				<div id="login-content">
				<?php require $content_tpl; ?>
				</div>
			</div> <!-- middle -->
		</div> <!-- container -->
		<div id="footer-wrap">
			<div id="footer">
			   	<p>Copyright &copy; <?php echo date("Y"); ?> <a href="https://www.Caboanchor.com" target="_blank">Los Cabos Anchor</a></p>
	        </div>
        </div>
	</body>
</html>