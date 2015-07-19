<?php
require_once("controllers.php");
require_once("models.php");

if (loginController::isLoggedin() == "false") {
	header('Location: login');
} else if (Users::isNewUser() == "false") {
	header('Location: ./');
}

if (isset($_POST['submit'])) {
	$username = loginController::getUsername();
	$email = $_POST['email'];
	$secq = $_POST['secq'];
	$seca = strtolower($_POST['seca']);
	$seca = sha1($seca);
	$userID = $_COOKIE['ARES_UID'];
	$submit = "true";
	if (in_array($userID, TempIDs::getAllIDs())) {
		TempIDs::deleteID($_COOKIE['ARES_UID']);
	} else {
		$submit = "false";
		session_start();
		$_SESSION['status'] = "mismatch";
		session_write_close();
		unset($_COOKIE['ARES']);
		setcookie('ARES', '', time() - 7200);
		if (isset($_COOKIE['ARES_UID'])) {
			TempIDs::deleteID($_COOKIE['ARES_UID'], $username);
			unset($_COOKIE['ARES_UID']);
			setcookie('ARES_UID', '', time() - 3600);
		}
	}
	if ($submit == "true") {
		Users::addUser();
		session_start();
		$_SESSION['status'] = "welcome";
		session_write_close();
		header('Location: ./');
	} else {
		header('Location: login');
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Register</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h2>Welcome <?=loginController::getUsername()?>!</h2>
				<p>We noticed that this was your first time logging in. Please take a moment to submit the form below in order to continue.</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-push-4">
			<h2>Register</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>Username</label>
					<input type="text" class="form-control" value="<?=loginController::getUsername()?>" disabled>
				</div>
				<div class="form-group">
					<label>E-mail Address</label>
					<input type="text" name="email" class="form-control" placeholder="example@xgenstudios.com" required>
				</div>
				<div class="form-group">
					<label>Security Question</label>
					<input type="text" name="secq" class="form-control" placeholder="Example: Who has the worst hairline on SA?" required>
				</div>
				<div class="form-group">
					<label>Security Answer</label>
					<input type="text" name="seca" class="form-control" placeholder="Example: Afroman32" required>
				</div>
				<input type="submit" name="submit" class="btn btn-primary" value="Submit">
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>