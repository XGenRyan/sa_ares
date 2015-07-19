<?php
require_once("models.php");
require_once("controllers.php");

loginController::redirectUser();

session_start();
if (isset($_SESSION['status'])) {
	if ($_SESSION['status'] == "mismatch") {
		session_unset($_SESSION['status']);
		session_destroy();
		$msg = 'User ID mismatch. Please clear your cookies/cache and try to register again.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	}
} else {
	$status = "";
}
session_write_close();

if (isset($_POST['submit'])) {
	$username = strtolower($_POST['username']);
	$username = ucfirst($username);
	$password = $_POST['password'];

	loginController::APIauth();
		
	if ($authSuccess == "true") {
		Users::changeName($userID, $username);
		Users::updateLastLogin();
		loginController::setCookie();
		if (Users::isNewUser($username) == "true") {
			TempIDS::addID($userID, $username);
			setcookie('ARES_UID', $userID, time()+3600);
		}
		header('Location: ./');
	} else {
		$msg = 'Invalid username/password combination.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Login</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="col-md-5 col-md-offset-3">
			<?=$status?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-md-offset-4">
			<h2>Login</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>SA Username</label>
					<input class="form-control" name="username" placeholder="Username" type="text">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input class="form-control" name="password" placeholder="Password" type="password">
				</div>
				<input class="btn btn-primary" type="submit" name="submit" value="Sign in">
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>