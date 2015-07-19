<?php
require_once("controllers.php");
require_once("models.php");

registerController::redirectUser();

$status = "";

if (loginController::isLoggedin() == "false") {
	header('Location: login');
}

if (isset($_POST['submit'])) {
	$username = loginController::getUsername();
	$newemail = $_POST['newemail'];
	$seca = strtolower($_POST['seca']);
	$seca = sha1($seca);

	$save = "true";

	if ($newemail == Users::getEmailAddress()) {
		$msg = 'Your new e-mail address is the same as the one we currently have on record.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
		$save = "false";
	} else if ($save == "true" && $seca == Users::getSecurityAnswer()) {
		Users::updateEmailAddress();

		session_start();
		if (isset($_SESSION['status'])) {
			if ($_SESSION['status'] == "update") {
				session_unset();
				session_destroy();
				$msg = 'Your e-mail address has been updated.';
				$status = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
			}
		}
		session_write_close();
	} else if ($save == "true" && $seca != Users::getSecurityAnswer()) {
		$msg = 'Incorrect security answer.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else {
		$msg = 'Whoops, looks like something went wrong.';
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
	<title>Ares - Change E-mail Address</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-push-4">
			<?=$status?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-push-4">
			<h2>Change E-mail Address</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>Username</label>
					<input type="text" class="form-control" value="<?=loginController::getUsername()?>" disabled>
				</div>
				<div class="form-group">
					<label>Current E-mail Address</label>
					<input type="text" class="form-control" value="<?=Users::getEmailAddress()?>" disabled>
				</div>
				<div class="form-group">
					<label>New E-mail Address</label>
					<input type="text" name="newemail" class="form-control" required>
				</div>
				<div class="form-group">
					<label>Security Question</label>
					<input type="text" class="form-control" value="<?=Users::getSecurityQuestion()?>" disabled>
				</div>
				<div class="form-group">
					<label>Security Answer</label>
					<input type="text" name="seca" class="form-control" required>
				</div>
				<input type="submit" name="submit" class="btn btn-primary" value="Update">
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>