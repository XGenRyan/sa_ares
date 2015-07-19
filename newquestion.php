<?php
require_once("models.php");
require_once("controllers.php");
$key = $_GET['s'];

registerController::redirectUser();

if (ResetQuestion::doesKeyExist() == "false") {
	session_start();
	$_SESSION['status'] = "newquestion";
	session_write_close();
	header('Location: ./');
} else if (ResetQuestion::getUsername() != loginController::getUsername()) {
	ResetQuestion::deleteEntry();
	header('Location: login');
}

if (isset($_POST['submit'])) {
	$username = loginController::getUsername();
	$secq = $_POST['secq'];
	$seca = strtolower($_POST['seca']);
	$seca = sha1($seca);
	Users::updateSecurity();
	ResetQuestion::deleteEntry();
	$msg = 'Your security question has been updated.';
	$status = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - New Security Question</title>
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
			<h2>New Security Question</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>Username</label>
					<input type="text" class="form-control" value="<?=loginController::getUsername()?>" disabled>
				</div>
				<div class="form-group">
					<label>Security Question</label>
					<input type="text" name="secq" class="form-control" required>
				</div>
				<div class="form-group">
					<label>Security Answer</label>
					<input type="text" name="seca" class="form-control" required>
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