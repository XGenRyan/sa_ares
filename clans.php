<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

$path = ltrim($_SERVER['REQUEST_URI'], '/');
$elements = explode('/', $path);

$max = $_ENV['clans_max_elements'];
$min = $_ENV['clans_min_elements'];

if ($_SERVER['REQUEST_URI'] == $_ENV['clans_path'] || count($elements) == $min || count($elements) >= $max) {
	header('Location: ../');
}

$clan = strtolower($elements[$min]);

if (strpos($clan, '%20') !== false) {
	$clan = str_replace('%20', ' ', $clan);
}

$clanlist = array_map('strtolower', Clans::getClanList());

$key = array_search($clan, $clanlist);

$original = Clans::getClanList()[$key];

if (!in_array($clan, $clanlist)) {
	header('Location: ../');
}

if (Clans::isLeader() == "true" && $original != Users::getClan() && WarDeclares::inProgress(Users::getClan()) == "false" && WarDeclares::inProgress($original) == "false") {
	session_start();
	$_SESSION['tempclan'] = $original;
	session_write_close();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Clan: <?=$original?></title>
	<link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="../css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="../css/main.css" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.14/angular.min.js"></script>
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
</head>
<body ng-app="main" ng-controller="ClanDetails">
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="../">Ares</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-left">
				<?php
				if (loginController::isLoggedin() == "true" &&  Users::hasClan() == "true" && Users::isNewUser() == "false") {
					echo '<li><a href="'.strtolower(Users::getClan()).'">My Clan</a></li>';
				}

				if (loginController::isLoggedin() == "true" && Users::hasClan() == "true" && Clans::isLeader() == "true") {
					echo '<li><a href="../manage">Manage Clan</a></li>';
				}

				if (loginController::isLoggedin() == "true" && Users::hasClan() == "true" && Clans::isLeader() == "true" && Queue::inProgress(Users::getClan()) == "true") {
					if (Queue::newMessageExists(Users::getClan()) == "true") {
						$badge = ' <span class="badge">new</span>';
					} else {
						$badge = "";
					}
					echo '<li><a href="../queue">Queue'.$badge.'</a></li>';
				}

				if (loginController::isLoggedin() == "true") {
					echo '<li><a href="../contact">Contact</a></li>';
				}
				?>
				<li><a href="../docs">Docs</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<?php
			if (in_array(loginController::getUsername(), Staff::getStaffMembers())) {
				$modcp = '<li><a href="../modcp">Mod CP</a></li><li class="divider"></li>';
			} else {
				$modcp= "";
			}
			if (loginController::isLoggedin() == "false") {
				echo '<li><a href="../login">Login</a></li>'; 
			} else {
				echo '<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.loginController::getUsername().' <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						'.$modcp.'
						<li><a href="../update">Update E-mail Address</a></li>
						<li><a href="../reset">Reset Security Questions</a></li>
						<li class="divider"></li>
						<li><a href="../logout">Logout</a></li>
					</ul>
				</li>';
			}
			?>
			</ul>
		</div>
	</div>
</nav>
<div class="container">
	<div class="row">
		<?php
		if ($original == Users::getClan() && Clans::isLeader() == "false") {
			$offset = "7-1";
		} else if ($original != Users::getClan() && Clans::isLeader() == "true" && WarDeclares::inProgress(Users::getClan()) == "false" && WarDeclares::inProgress($original) == "false") {
			$offset = "6-8";
		} else if (ClanRequests::inProgress() == "true" && Users::hasClan() == "false") {
			$offset = "5-9";
		} else if (loginController::isLoggedin() == "true" && Users::hasClan() == "false" && Clans::getNumMembers($original) >= 15) {
			$offset = 7;
		} else {
			$offset = 6;
		}
		?>
		<div class="col-md-5 col-md-offset-<?=$offset?>">
			<?php
			if ($original == Users::getClan() && Clans::isLeader() == "false") {
				echo '<p><a href="" id="quit" class="btn btn-danger btn-lg" role="button">Quit Clan</a></p>';
			}

			if ($original != Users::getClan() && Clans::isLeader() == "true" && WarDeclares::inProgress(Users::getClan()) == "false" && WarDeclares::inProgress($original) == "false") {
				echo '<p><a href="../settings" class="btn btn-success btn-lg" role="button">Declare War</a></p>';
			}

			if (loginController::isLoggedin() == "false") {
				$joinLink = '<a href="../join" class="btn btn-primary btn-lg" role="button">Join Clan</a>';
			} else if (loginController::isLoggedin() == "true" && Users::hasClan() == "false" && JoinRequests::inProgress() == "true") {
				$joinLink = '<a class="btn btn-primary btn-lg" role="button">Pending...</a>';
			} else if (loginController::isLoggedin() == "true" && Users::hasClan() == "false" && Clans::getNumMembers($original) < 15) {
				$joinLink = '<a href="" id="join" class="btn btn-primary btn-lg" role="button">Join Clan</a>';
			}

			if (ClanRequests::inProgress() == "true" && Users::hasClan() == "false") {
				echo '<p>'.$joinLink.' <a class="btn btn-success btn-lg" role="button">Processing...</a></p>';
			} else if (Users::hasClan() == "false" || loginController::isLoggedin() == "false") {
				echo '<p>'.$joinLink.' <a href="../create" class="btn btn-success btn-lg" role="button">Create Clan</a></p>';
			}
			?>
		</div>
	</div>
	<div class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<table class="table table-striped table-hover" ng-repeat="clan in clans">
				<tr>
					<th colspan="2">{{clan.name}}</th>
				</tr>
				<tr>
					<td width="50%">
						<img ng-src="{{clan.logo}}" height="100px" width="100px" />
					</td>
					<td width="50%">
						<blockquote>{{clan.motto}}</blockquote>
					</td>
				</tr>
				<script>
				</script>
				<?php
				if (Clans::isFieldEmpty('website', $original) == "false") {
					echo '<tr>
						<th>Website</th>
						<td align="right"><a ng-href="{{clan.website}}" target="_blank">{{clan.website}}</a></td>
					</tr>';
				}
				?>
				<tr>
					<th>Rank</th>
					<td align="right"><?=Clans::getRank($original)?></td>
				</tr>
				<tr>
					<th>Points</th>
					<td align="right">{{clan.points | number:0}}</td>
				</tr>
				<tr>
					<th>Leader</th>
					<td align="right">{{clan.leader}}</td>
				</tr>
				<tr>
					<th>Members</th>
					<td align="right">{{clan.members}}</td>
				</tr>
				<tr>
					<th>Date Created</th>
					<td align="right">{{clan.date}}</td>
				</tr>
				<tr>
					<th>Win-Loss</th>
					<td align="right">{{clan.wins}}-{{clan.losses}}</td>
				</tr>
				<?php
				if (Clans::getWins($original) == 0) {
					echo '<tr>
						<th>W/L</th>
						<td align="right">0%</td>
					</tr>';
				} else {
					echo '<tr>
						<th>W/L</th>
						<td align="right">{{(parseInt(clan.wins)/(parseInt(clan.wins)+parseInt(clan.losses)))*100 | number:0}}%</td>
					</tr>';
				}
				?>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-md-offset-3-5">
			<a href="" id="viewroster" class="btn btn-default" role="button">View Roster</a>
			<table id="roster" class="table table-bordered table-striped table-hover">
				<tr class="info">
					<th>Roster</th>
				</tr>
				<tr ng-repeat="clan in clans">
					<td>{{clan.leader}}</td>
				</tr>
				<tr ng-repeat="member in members">
					<td>{{member.username}}</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script>
$(document).ready(function () {
	function isOdd(num) { 
		return num % 2;
	}
	var numbr = 1;
	$("#roster").hide();
	$("#viewroster").click(function(e) {
		e.preventDefault();
		$("#roster").toggle(
			function() {
				if (isOdd(numbr)) {
					$("#viewroster").html("Hide Roster");
				} else {
					$("#viewroster").html("View Roster");
				}
				numbr++;
		});
	});
});

$("#join").click(function() {
	var proceed = confirm("Are you sure you want to join this clan?");
	if (proceed == true) {
		$.ajax({
			url: "../join.php",
			dataType: "html",
			async: false,
			type: "POST",
			data: {clan:'<?=$original?>'},
			success: function() {
				alert("Success, your request to join this clan has been sent to the leader.");
				location.reload();
			},
			error: function() {
				alert("Whoops! Looks like something went wrong. Try again later.");
				location.reload();
			}
		});
	}
});

$("#quit").click(function() {
	var proceed = confirm("Are you sure you want to quit this clan?");
	if (proceed == true) {
		$.ajax({
			url: "../quit.php",
			dataType: "html",
			async: false,
			type: "POST",
			data: {clan:'<?=$original?>'},
			success: function() {
				alert("Success, you are no longer a member of this clan.");
				location.reload();
			},
			error: function() {
				alert("Whoops! Looks like something went wrong. Try again later.");
				location.reload();
			}
		});
	}
});
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<?php require_once("js/ng-clans-controller.php"); ?>
</body>
</html>