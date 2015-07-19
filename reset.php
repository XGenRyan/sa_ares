<?php
require_once("controllers.php");
require_once("models.php");

registerController::redirectUser();

$success = "";

if (loginController::isLoggedin() == "false") {
	header('Location: login');
}

if (isset($_POST['submit'])) {
	ResetQuestion::sendReset();

	session_start();
	if (isset($_SESSION['status'])) {
		if ($_SESSION['status'] == "reset") {
			session_unset();
			session_destroy();
			$msg = 'Check '.Users::getEmailAddress().' for the reset link.';
			$success = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
		}
	}
	session_write_close();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Reset Security Question</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	 <div class="row">
		<div class="col-md-4 col-md-offset-3-5">
			<?=$success?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5 col-md-offset-3-5">
			<h2>Reset Security Question</h2>
			<p>A link will be sent to your e-mail address with the information needed to reset your security question. Are you sure you would like to continue?</p>
			<form action="" method="POST">
				<input type="submit" name="submit" class="btn btn-default" value="Yes">
				<a href="./" class="btn btn-default">No</a>
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>