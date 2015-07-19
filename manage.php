<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false") {
	header('Location: ./');
}

$clan = Users::getClan();

session_start();
if (isset($_SESSION['status'])) {
	if ($_SESSION['status'] == "updated") {
		session_unset();
		session_destroy();
		$msg = 'Clan information has been updated.';
		$status = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "mottoERROR") {
		session_unset();
		session_destroy();
		$msg = 'Motto exceeds 120 characters.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "websiteERROR") {
		session_unset();
		session_destroy();
		$msg = 'Please shorten your website URL to 45 characters.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "logoERROR") {
		session_unset();
		session_destroy();
		$msg = 'Please shorten your logo URL to 200 characters.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "securityERROR") {
		session_unset();
		session_destroy();
		$msg = 'Wrong security answer.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "memberERROR") {
		session_unset();
		session_destroy();
		$msg = 'Invalid user specified.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	}
} else {
	$status = "";
}
session_write_close();

if (isset($_POST['save'])) {
	if (!empty($_POST['newwebsite']) && isset($_POST['newwebsite'])) {
		if (strlen($_POST['newwebsite']) <= 45) {
			if (strpos($_POST['newwebsite'], 'http://') === false && strpos($_POST['newwebsite'], 'https://') === false) {
				$_POST['newwebsite'] = 'http://'.$_POST['newwebsite'];
			}
			Clans::updateField("website", $_POST['newwebsite'], $clan);
		} else {
			session_start();
			$_SESSION['status'] = "websiteERROR";
			session_write_close();
		}
	}

	if (!empty($_POST['newmotto']) && isset($_POST['newmotto'])) {
		if (strlen($_POST['newmotto']) <= 120) {
			Clans::updateField("motto", $_POST['newmotto'], $clan);
		} else {
			session_start();
			$_SESSION['status'] = "mottoERROR";
			session_write_close();
		}
	}

	if (!empty($_POST['newlogo']) && isset($_POST['newlogo'])) {
		if (strlen($_POST['newlogo']) <= 200) {
			Clans::updateField("logo", $_POST['newlogo'], $clan);
		} else {
			session_start();
			$_SESSION['status'] = "logoERROR";
			session_write_close();
		}
	}

	header('Location: manage');
}

if (isset($_POST['delete'])) {
	if (sha1(strtolower($_POST['seca'])) == Users::getSecurityAnswer()) {
		Clans::deleteClan($clan);
		header('Location: ./');
	} else {
		session_start();
		$_SESSION['status'] = "securityERROR";
		session_write_close();
		header('Location: manage');
	}
}

if (isset($_POST['transfer'])) {
	$seca = strtolower($_POST['seca']);
	$seca = sha1($seca);
	$newleader = $_POST['newleader'];
	if ($seca == Users::getSecurityAnswer()) {
		if (ClanMembers::isaMember($newleader) == "true") {
			Clans::transferLeader($newleader, $clan);
			header('Location: ./');
		} else {
			session_start();
			$_SESSION['status'] = "memberERROR";
			session_write_close();
			header('Location: manage');
		}
	} else {
		session_start();
		$_SESSION['status'] = "securityERROR";
		session_write_close();
		header('Location: manage');
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Manage Clan</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.14/angular.min.js"></script>
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
</head>
<body ng-app="main" ng-controller="ManageClan">
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="col-md-5 col-md-offset-3-5">
			<?=$status?>
		</div>
	</div>
	<div class="row top-buffer">
		<div class="col-md-6 col-md-offset-3-5">
			<ul class="nav nav-pills">
				<li class="profile active"><a class="profile" href="">Clan Profile</a></li>
				<li class="members active"><a class="members" href="">Members <span class="badge" ng-bind="numMembers"></span></a></li>
				<li class="requests active"><a class="requests" href="">Requests <span class="badge" ng-bind="numRequests"></span></a></li>
				<li class="settings active"><a class="settings" href="">Settings</a></li>
			</ul>
		</div>
	</div>
	<div id="profile" class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<form action="manage" method="POST">
				<div class="form-group">
					<label>Website</label>
					<input type="text" class="form-control new-field" name="newwebsite" ng-model="newwebsite">
				</div>
				<div class="form-group">
					<label>Motto</label>
					<textarea class="form-control new-field" name="newmotto" ng-model="newmotto" maxlength="120"></textarea>
				</div>
				<div class="form-group">
					<label>Logo URL</label>
					<input type="text" class="form-control" name="newlogo" ng-model="newlogo">
				</div>
				<input type="submit" id="save" name="save" class="btn btn-success" value="Save Changes">
			</form>
			<table id="clantable" class="table table-striped table-hover top-buffer" ng-repeat="clan in clans">
				<tr>
					<th colspan="2">{{clan.name}}</th>
				</tr>
				<tr>
					<td width="50%">
						<img ng-if="!newlogo.length" ng-src="{{clan.logo}}" height="100px" width="100px" />
						<img ng-if="newlogo.length" ng-src="{{newlogo}}" height="100px" width="100px" />
					</td>
					<td width="50%" id="motto">
						<blockquote ng-if="!newmotto.length">{{clan.motto}}</blockquote>
						<blockquote ng-if="newmotto.length">{{newmotto}}</blockquote>
					</td>
				</tr>
				<tr>
					<th>Website</th>
					<td ng-if="!newwebsite.length" align="right"><a ng-href="{{clan.website}}" target="_blank">{{clan.website}}</a></td>
					<td ng-if="newwebsite.length" align="right"><a ng-href="{{newwebsite}}" target="_blank">{{newwebsite}}</a></td>
				</tr>
				<tr>
					<th>Rank</th>
					<td align="right"><?=Clans::getRank($clan)?></td>
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
				if (Clans::getWins($clan) == 0) {
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
	<div id="members" class="row top-buffer" ng-hide="noMembers()">
		<div class="col-md-5 col-md-offset-3-5">
			<table id="roster" class="table table-striped table-hover">
				<tr>
					<th>Roster</th>
				</tr>
				<tr ng-repeat="member in members">
					<td>{{member.username}} <a ng-click="kick($index)"><span class="glyphicon glyphicon-remove red float-right"></span></a></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="requests" class="row top-buffer" ng-hide="!requests.length">
		<div class="col-md-5 col-md-offset-3-5">
			<table class="table table-striped table-hover">
				<tr>
					<th width="10%">#</th>
					<th>Name</th>
					<th width="10%"></th>
					<th width="10%"></th>
				</tr>
				<tr ng-repeat="request in requests">
					<td>{{$index + 1}}</td>
					<td>{{request.username}}</td>
					<td><a ng-click="add($index)"><span class="glyphicon glyphicon-ok green"></span></a></td>
					<td><a ng-click="delete($index)"><span class="glyphicon glyphicon-remove red"></span></a></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="settings" class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<form action="manage" method="POST" onsubmit="return confirm('Are you sure you want to continue? There is no way to undo this...');">
				<div class="form-group">
					<label>Security Question</label>
					<input type="text" class="form-control" value="<?=Users::getSecurityQuestion()?>" disabled>
				</div>
				<div class="form-group">
					<label>Security Answer</label>
					<input type="text" name="seca" class="form-control" required>
				</div>
				<div>
					<input type="submit" name="delete" class="btn btn-danger" value="Delete Clan">
				</div>
				<div class="form-group top-buffer" ng-if="!noMembers()">
					<label>Change Leadership</label>
					<select class="form-control" name="newleader" ng-model="newleader">
						<option ng-repeat="member in members" value="{{member.username}}">{{member.username}}</option>
					</select>
					<input ng-bind="newleader" type="submit" name="transfer" class="btn btn-success top-buffer" value="Transfer Clan to {{newleader}}">
				</div>
			</form>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<?php require_once("js/ng-manage-controller.php"); ?>
<script>
$("#members").hide();
$("li.members").removeClass("active");
$("#requests").hide();
$("li.requests").removeClass("active");
$("#settings").hide();
$("li.settings").removeClass("active");

$("textarea[maxlength]").each(function() {
	var maxLength = parseInt($(this).attr('maxlength'));
	$(this).attr('maxlength', null);

	var el = $("<span class=\"character-count\">" + maxLength + "</span>");
	el.insertAfter($(this));

	$(this).bind('keyup keydown', function() {
		var cc = $(this).val().length;

		el.text(maxLength - cc);

		if(maxLength < cc) {
			el.css('color', 'red');
		} else {
			el.css('color', '');
		}
	});
});

function toggleNav(item) {
	var divs = ["#profile", "#members", "#requests", "#settings"];
	var index = divs.indexOf(item);
	divs.splice(index, 1);

	var i;
	for (i = 0; i < divs.length; ++i) {
		$(divs[i]).hide();

		divs[i] = divs[i].replace("#", "");
		$("li."+divs[i]).removeClass("active");
	}

	$(item).show();

	item = item.replace("#", "");
	$("li."+item).addClass("active");
}

$(".profile").click(function() {
	toggleNav("#profile");
});

$(".members").click(function() {
	toggleNav("#members");
});

$(".requests").click(function() {
	toggleNav("#requests");
});

$(".settings").click(function() {
	toggleNav("#settings");
});
</script>
</body>
</html>