<?php
require_once("models.php");
require_once("controllers.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Page not found - Ares</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<style>
	p {
		margin-top: 30px;
	}
	iframe {
		min-width: 560px;
		width: 100%;
		height: 315px;
	}
	</style>
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="col-md-5 col-md-offset-3-5" style="text-align: center">
			<p><h1 style="font-weight: bold">Oops, page not found!</h1></p>
			<p><a href="./" class="btn btn-success btn-lg">Take me home</a></p>
			<p><h2>... or you can stay here and watch this goat scream like a human.</h2></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5 col-md-offset-3">
			<iframe src="http://www.youtube.com/embed/SIaFtAKnqBU?vq=hd720&amp;rel=0&amp;showinfo=0&amp;controls=0&amp;iv_load_policy=3&amp;loop=1&amp;playlist=SIaFtAKnqBU&amp;modestbranding=1&amp;autoplay=1" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>
		</div>
	</div>
</div>
</body>
</html>