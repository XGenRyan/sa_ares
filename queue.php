<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

$clan = Users::getClan();

if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false" || Queue::inProgress($clan) == "false") {
	header('Location: ./');
}

$time = new DateTime();
$time = $time->format('Y-m-d H:i:s');

Queue::updateLastRead($clan, $time);

$keys = ["id", "attacker", "defender", "type", "rounds", "map", "tac", "fks", "guns", "melee", "hammercamps", "running", "stalling", "podcamps", "taor", "created_at"];

$declare = array_combine($keys, WarDeclares::getEverything($clan));

$keys2 = ["id", "attacker", "defender", "a_p1", "a_p2", "a_wins", "d_p1", "d_p2", "d_wins", "tac", "a_ready", "d_ready", "started", "created_at", "updated_at"];

StatMonitor::getEverything($clan) != NULL ? $statmonitor = array_combine($keys2, StatMonitor::getEverything($clan)) : $statmonitor = "";

if (isset($_POST['cancel']) || isset($_POST['decline'])) {
	warController::eraseWar();
	header('Location: ./');
}

if (isset($_POST['accept'])) {
	$attacker = Queue::getSender($clan);
	$defender = $clan;
	$d_p1 = $_POST['player1'];
	isset($_POST['player2']) ? $d_p2 = $_POST['player2'] : $d_p2 = NULL;
	
	if (warController::arePlayersValid($d_p1, $d_p2) == "true") {
		warController::acceptWar();
		header('Location: ./');
	} else {
		header('Location: queue');
	}
}

if (isset($_POST['lock'])) {
	$attacker = $clan;
	$defender = Queue::getSender($clan);
	$a_p1 = $_POST['player1'];
	isset($_POST['player2']) ? $a_p2 = $_POST['player2'] : $a_p2 = NULL;

	if (warController::arePlayersValid($a_p1, $a_p2) == "true") {
		warController::updateWar();
		header('Location: ./');
	} else {
		header('Location: queue');
	}
}

if (isset($_POST['ready'])) {
	StatMonitor::updateReady($clan);
	header('Location: queue');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Queue</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row top-buffer">
		<?php 
		require_once('partials/_queue_war_declare.php');
		require_once('partials/_queue_war_selection.php');
		require_once('partials/_queue_war_ready.php');
		require_once('partials/_queue_war_started.php');
		?>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script>
if ($("#roster").val() == $("#roster2").val())
	$("#roster2 option:selected").remove();

var array = [];
$("#roster option").each(function() {
	array.push($(this).val());
});

if ($.inArray($("#roster2 option:selected").val().toString(), array) != -1) {
	var selectobject=document.getElementById("roster");
	for (var i=0; i< selectobject.length; i++) {
		if (selectobject.options[i].value == $("#roster2 option:selected").val())
			selectobject.remove(i);
	}
}

$("select").change(function() {
	if ($("#roster option:selected").val() == $("#roster2 option:selected").val())
	location.reload();
});
</script>
</body>
</html>