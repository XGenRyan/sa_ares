<?php
require_once("classes.php");

$result = "";

if (isset($_POST['submit'])) {
	if (is_numeric($_POST['points1']) && is_numeric($_POST['points2'])) {
		$Attacking = new Glicko2Clan($rating = $_POST['points1']);
		$Defending = new Glicko2Clan($rating = $_POST['points2']);

		$Attacking->AddWin($Defending);
		$Defending->AddLoss($Attacking);

		$Attacking->Update();
		$Defending->Update();

		$a_win_points = round($Attacking->rating);
		$a_win_points_diff = round($a_win_points - $_POST['points1']);
		$d_lose_points = round($Defending->rating);
		$d_lose_points_diff = round($_POST['points2'] - $d_lose_points);

		$Attacking2 = new Glicko2Clan($rating = $_POST['points1']);
		$Defending2 = new Glicko2Clan($rating = $_POST['points2']);

		$Attacking2->AddLoss($Defending2);
		$Defending2->AddWin($Attacking2);

		$Attacking2->Update();
		$Defending2->Update();

		$a_lose_points = round($Attacking2->rating);
		$a_lose_points_diff = round($_POST['points1'] - $a_lose_points);
		$d_win_points = round($Defending2->rating);
		$d_win_points_diff = round($d_win_points - $_POST['points2']);

		$result = '<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="jumbotron">
					<h2>Result</h2>
					<p>If Clan One wins they will have '.$a_win_points.' points <font color="green">(+'.$a_win_points_diff.')</font> and Clan Two will have '.$d_lose_points.' points <font color="red">(-'.$d_lose_points_diff.')</font>.</p>
					<p>If Clan Two wins they will have '.$d_win_points.' points <font color="green">(+'.$d_win_points_diff.')</font> and Clan One will have '.$a_lose_points.' points <font color="red">(-'.$a_lose_points_diff.')</font>.</p>
				</div>
			</div>
		</div>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Point Calculator</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<?=$result?>
	<div class="row top-buffer" style="margin-top: 20px">
		<div class="col-md-3 col-md-offset-4">
			<h2>Point Estimator</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>Clan One's Points</label>
					<input class="form-control" name="points1" type="number">
				</div>
				<div class="form-group">
					<label>Clan Two's Points</label>
					<input class="form-control" name="points2" type="number">
				</div>
				<input class="btn btn-primary" type="submit" name="submit" value="Calculate">
			</form>
		</div>
	</div>
</div>
<script>
$("input[type='number']").keydown(function (e) {
	if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 40, 190]) !== -1 ||
		(e.keyCode == 65 && e.ctrlKey === true) || 
		(e.keyCode >= 35 && e.keyCode <= 39)) {
			return;
	}
	if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	}
});
</script>
</body>
</html>