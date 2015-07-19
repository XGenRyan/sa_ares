<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

$error = "";

if (loginController::isLoggedin() == "false") {
	header('Location: login');
} else if (ClanRequests::inProgress() == "true" || Users::hasClan() == "true") {
	header('Location: ./');
}

if (isset($_POST['submit'])) {
	$clanname = $_POST['clanname'];
	$username = loginController::getUsername();
	$website = $_POST['website'];
	if (empty($website)) {
		$website = "";
	} else {
		if (strpos($website, 'http://') === false && strpos($website, 'https://') === false) {
			$website = 'http://'.$website;
		}
	}
	$seca = strtolower($_POST['seca']);
	$seca = sha1($seca);

	$legal = preg_match('/^[a-z0-9 _.,.\-]+$/i', $clanname);
	if (strlen($clanname) >= 3 && strlen($clanname) <= 16 && $legal == 1 && !in_array(strtolower($clanname), array_map('strtolower', Clans::getClanList()))) {
		$save = "true";
	} else if (in_array(strtolower($clanname), array_map('strtolower', Clans::getClanList()))) {
		$save = "false";
		$msg = 'Clan name is taken.';
		$error = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else {
		$save = "false";
		$msg = 'Clan name must be between 3 and 16 characters and only contain letters, numbers, periods, commas, spaces or underscores.';
		$error = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	}

	if ($save == "true" && $seca == Users::getSecurityAnswer()) {
		ClanRequests::createClan();
		header('Location: ./');
	} else if ($save == "true") {
		$msg = 'Incorrect security answer.';
		$error = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Create Clan</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="jumbotron">
			<h2>Apply for a clan</h2>
			<p>In order to prevent the creation of alt clans we require everyone to fill out the request form below. It might take a few days for us to process and verify your request, so please be patient.</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-push-4">
			<?=$error?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-push-4">
			<h2>Create a clan</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>Clan Leader</label>
					<input type="text" class="form-control" placeholder="<?=loginController::getUsername()?>" disabled>
				</div>
				<div class="form-group">
					<label>Clan Name</label>
					<input type="text" name="clanname" class="form-control" required>
				</div>
				<div class="form-group">
					<label>Website</label>
					<input type="text" name="website" class="form-control" placeholder="Optional">
				</div>
				<div class="form-group">
					<label>Security Question</label>
					<input type="text" class="form-control" placeholder="<?=Users::getSecurityQuestion()?>" disabled>
				</div>
				<div class="form-group">
					<label>Security Answer</label>
					<input type="text" name="seca" class="form-control" required>
				</div>
				<input class="btn btn-primary" type="submit" name="submit" value="Create">
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>