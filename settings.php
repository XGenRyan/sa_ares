<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

session_start();
$tempclan = $_SESSION['tempclan'];
session_write_close();

if (!in_array($tempclan, Clans::getClanList())) {
	header('Location: ./');
}

$clan = Users::getClan();

if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false" || WarDeclares::inProgress($clan) == "true" || WarDeclares::inProgress($tempclan) == "true") {
	header('Location: ./');
}

if (strtolower($tempclan) == strtolower($clan)) {
	header('Location: ./');
}

$maps = array("XGen HQ", "Sunnyvale Trailer Park", "Toxic Spillway", "Workplace Anxiety", "Storage Yard", "Green Labryrinth", "Floor Thirteen", "The Pit", "Industrial Drainage", "GlobalMegaCorp LTD", "Concrete Jungle", "Nuclear Underground", "Unstable Terrace", "Office Space", "The Foundation", "Brawlers Burrow", "Trench Run", "Corporate Wasteland", "Sewage Treatment", "Storm Drain", "LP Map");

if (isset($_POST['submit'])) {
	$submit = "true";

	$attacker = $clan;
	$defender = $tempclan;
	$type = $_POST['type'];
	$rounds = $_POST['rounds'];
	$map = $_POST['map'];

	if (isset($_POST['tac'])) {
		$tac = 1;
	} else {
		$tac = 0;
	}

	if (isset($_POST['nfk'])) {
		$fks = 0;
	} else {
		$fks = 1;
	}
	if (isset($_POST['ng'])) {
		$guns = 0;
	} else {
		$guns = 1;
	}
	if (isset($_POST['nm'])) {
		$melee = 0;
	} else {
		$melee = 1;
	}
	if (isset($_POST['nhc'])) {
		$hammercamps = 0;
	} else {
		$hammercamps = 1;
	}
	if (isset($_POST['nr'])) {
		$running = 0;
	} else {
		$running = 1;
	}
	if (isset($_POST['ns'])) {
		$stalling = 0;
	} else {
		$stalling = 1;
	}
	if (isset($_POST['npc'])) {
		$podcamps = 0;
	} else {
		$podcamps = 1;
	}
	if (isset($_POST['taor'])) {
		$taor = 1;
	} else {
		$taor = 0;
	}

	if ($type != "1v1" && $type != "2v2") {
		$submit = "false";
	}

	if ($type == "2v2" && Clans::getNumMembers($clan) <= 1 || $type == "2v2" && Clans::getNumMembers($tempclan) <= 1) {
		$submit = "false";
	}

	if ($rounds != "2/3" && $rounds != "3/5") {
		$submit = "false";
	}

	if (!in_array($map, $maps)) {
		$submit = "false";
	}

	if ($submit == "true") {
		if (WarDeclares::inProgress($clan) == "false" && WarDeclares::inProgress($tempclan) == "false") {
			WarDeclares::submitDeclare();
			Queue::newQueue($attacker, $defender, "declare");
		}
	}

	header('Location: ./');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Settings</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.14/angular.min.js"></script>
</head>
<body ng-app>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<form action="settings" method="POST">
				<h4 class="title"><img src="<?=Clans::getLogo($clan)?>" height="40px" width="40px" /> <?=$clan?> <i>vs.</i> <img src="<?=Clans::getLogo($tempclan)?>" height="40px" width="40px" /> <?=$tempclan?></h4>
				<fieldset class="border">
					<legend class="border">Game Settings</legend>
					<?php Clans::getNumMembers($clan) > 1 && Clans::getNumMembers($tempclan) > 1 ? $type2v2 = '<br /><input type="radio" name="type" value="2v2" required> 2v2' : $type2v2 = ''; ?>
					<div class="form-group">
						<label>Type:</label>
						<div>
							<input type="radio" name="type" value="1v1" required> 1v1
							<?=$type2v2?>
						</div>
					</div>
					<div class="form-group">
						<label>Rounds:</label>
						<select class="form-control" name="rounds" required>
							<option value="2/3">2/3</option>
							<option value="3/5">3/5</option>
						</select>
					</div>
					<div class="form-group">
						<label>Map:</label>
						<select class="form-control" name="map" required>
						<?php 
						foreach($maps as $map) {
							echo '<option value="'.$map.'">'.$map.'</option>';
						}
						?>
						</select>
					</div>
					<div class="form-group">
						<label>Platform:</label>
						<div>
							<input type="checkbox" name="tac"> TAC (only check this if everyone will be using TAC)
						</div>
					</div>
					<div class="form-group">
						<label>Rules (check all that apply):</label>
						<div class="checkbox-group">
							<input type="checkbox" name="nfk"> No freekills
							<input type="checkbox" name="ng"> No guns
							<input type="checkbox" name="nm"> No melee
							<input type="checkbox" name="nhc"> No hammer camps
							<input type="checkbox" name="nr"> No running
							<input type="checkbox" name="ns"> No stalling
							<input type="checkbox" name="npc"> No pods camps
							<input type="checkbox" name="taor"> TAOR
						</div>
					</div>
				</fieldset>
				<input class="btn btn-success" type="submit" name="submit" value="Continue">
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>