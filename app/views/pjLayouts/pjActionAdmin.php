<!doctype html>
<html>
	<head>
		<title>Los Cabos Anchor</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].htmlspecialchars($css['file']).'" />';
		}
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].htmlspecialchars($js['file']).'"></script>';
		}
		?>
		<!--[if gte IE 9]>
  		<style type="text/css">.gradient {filter: none}</style>
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="../css/hheader.css">
		<link rel="stylesheet" type="text/css" href="../css/header11.css">
	</head>

	<body>
			
				<?php
					include ('../includes/header11.php');
				?>

			<div id="container">
			
			

				<div id="middle">
						<div id="leftmenu">
							<?php require PJ_VIEWS_PATH . 'pjLayouts/elements/leftmenu.php'; ?>
						</div>
					<div id="right">
						<div class="content-top"></div>
						<div class="content-middle" id="content">
						<?php require $content_tpl; ?>
					</div>
						<div class="content-bottom"></div>
						</div> <!-- content -->
					<div class="clear_both"></div>
				</div>

            </div>


		 	<!-- middle -->
				<!----insert footer here----------------->
				<?php
				include ('../includes/footer.php');
				?>

		<!--</div>--> <!-- container -->
		<!---<div id="footer-wrap">
			<div id="footer">
			   	<p>Copyright &copy; <?php echo date("Y"); ?> <a href="https://www.caboanchor.com" target="_blank">Los Cabos Transportation</a></p>
	        </div>
        </div>-->
	</body>
</html>