<?php
require_once("models.php");
require_once("controllers.php");

"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" == "http://trgx.org/" ? header('Location: http://ares.xgenstudios.com/') : "";

registerController::redirectUser();

session_start();
if (isset($_SESSION['status'])) {
	if ($_SESSION['status'] == "logout") {
		session_unset();
		session_destroy();
		$msg = 'You have logged out.';
		$status = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "newquestion") {
		session_unset();
		session_destroy();
		$msg = 'Invalid security key specified.';
		$status = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "welcome") {
		session_unset();
		session_destroy();
		$msg = 'Congrats, you are now officially registered.';
		$status = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
	} else if ($_SESSION['status'] == "ended") {
		session_unset();
		session_destroy();
		$msg = 'War has ended. Make sure your clan record was updated correctly.';
		$status = '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Notice!</strong> '.$msg.'</div>';
	}
} else {
	$status = "";
}
session_write_close();

if (isset($_GET['war'])) {
	if ($_GET['war'] == "done") {
		session_start();
		$_SESSION['status'] = "ended";
		session_write_close();
		header('Location: ./');
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="ares, ares clan system, xgen studios, stick arena, clan system, clans, sa clans, flash games, targex"/>
	<meta name="description" content="Ares is the official clan system for Stick Arena that enables you to find and join clans, as well as create them and play against other clans."/>
	<title>Ares - Home</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/ng-pagination.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.14/angular.min.js"></script>
	<script src="http://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.3.0.min.js"></script>
</head>
<body ng-app="main" ng-controller="ClanList">
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<?=$status?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5 col-md-offset-7">
			<?php
			if (Users::hasClan() == "false" && ClanRequests::inProgress() == "true") {
				echo '<p><a class="btn btn-success btn-lg" role="button">Processing...</a></p>';
			} else if (Users::hasClan() == "false" || loginController::isLoggedin() == "false") {
				echo '<p><a href="create" class="btn btn-success btn-lg" role="button">Create Clan</a></p>';
			}
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-md-offset-4">
			<div class="form-group">
			<label>Search</label>
				<input ng-model="name" type="text" class="form-control search" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<table class="table table-striped table-hover">
				<tr>
					<th width="10%">Rank</th>
					<th width="5%"></th>
					<th width="25%">Clan</th>
					<th width="5%">Members</th>
				</tr>
				<tr ng-repeat="clan in filteredclans | filter:name">
					<td class="vert-align">{{clan.rank}}</td>
					<td class="vert-align"><img ng-src="{{clan.logo}}" height="40px" width="40px" /></td>
					<td class="vert-align"><a ng-href="clans/{{clan.name | lowercase}}">{{clan.name}}</a></td>
					<td class="vert-align">{{clan.members}}</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 text-align left">
			<div data-pagination="" data-num-pages="numPages()" data-current-page="currentPage" data-max-size="maxSize" data-boundary-links="true" ng-click="name = ''"></div>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<?php require_once("js/ng-index-controller.php"); ?>
</body>
</html>