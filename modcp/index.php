<?php
require_once("../models.php");
require_once("../controllers.php");

if (loginController::isLoggedin() == "false") {
	if (isset($_COOKIE['ARES_staff'])) {
		unset($_COOKIE['ARES_staff']);
		setcookie('ARES_staff', '', time() - 3600);
	}
	header('Location: ../login');
}

if (loginController::isLoggedin() == "true") {
	if (!in_array(loginController::getUsername(), Staff::getStaffMembers())) {
		unset($_COOKIE['ARES_staff']);
		setcookie('ARES_staff', '', time() - 3600);
		header('Location: ../');
	}
}

session_start();
if (isset($_SESSION['result'])) {
	if ($_SESSION['result'] == "auth_success") {
		session_unset($_SESSION['result']);
		session_destroy();
		$msg = 'Authentication successful. Hi '.Staff::getUsername().'.';
		$result = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
	} else if ($_SESSION['result'] == "auth_error") {
		session_unset($_SESSION['result']);
		session_destroy();
		$msg = 'Invalid username/password combination.';
		$result = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Error!</strong> '.$msg.'</div>';
	} else if ($_SESSION['result'] == "created") {
		session_unset($_SESSION['result']);
		session_destroy();
		$msg = 'Clan was created.';
		$result = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
	} else if ($_SESSION['result'] == "created2") {
		session_unset($_SESSION['result']);
		session_destroy();
		$msg = 'User already has a clan so the request was just deleted.';
		$result = '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Warning!</strong> '.$msg.'</div>';
	} else if ($_SESSION['result'] == "deleted") {
		session_unset($_SESSION['result']);
		session_destroy();
		$msg = 'Clan request was deleted.';
		$result = '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> '.$msg.'</div>';
	}
} else {
	$result = "";
}
session_write_close();

if (isset($_POST['create'])) {
	$id = $_POST['id'];
	$leader = ClanRequests::getRequest($id)[2];
	$clanname = ClanRequests::getRequest($id)[1];
	$website = ClanRequests::getRequest($id)[3];
	if (Users::hasClan($leader) == "false") {
		staffController::createClan($id, $leader, $clanname, $website);
		session_start();
		$_SESSION['result'] = "created";
		session_write_close();
	} else {
		staffController::deleteRequest($id);
		session_start();
		$_SESSION['result'] = "created2";
		session_write_close();
	}
	header('Location: ../modcp');
}

if (isset($_POST['delete'])) {
	$id = $_POST['id'];
	staffController::deleteRequest($id);
	session_start();
	$_SESSION['result'] = "deleted";
	session_write_close();
	header('Location: ../modcp');
}

if (isset($_POST['mdelete'])) {
	$mid = $_POST['mid'];
	Contact::deleteMessage($mid);
	header('Location: ../modcp');
}

if (isset($_POST['submit'])) {
	$username = ucfirst(strtolower($_POST['username']));
	$password = $_POST['password'];
	$ip = getenv('HTTP_CLIENT_IP')?:
	getenv('HTTP_X_FORWARDED_FOR')?:
	getenv('HTTP_X_FORWARDED')?:
	getenv('HTTP_FORWARDED_FOR')?:
	getenv('HTTP_FORWARDED')?:
	getenv('REMOTE_ADDR');

	if (password_verify($password, Staff::getHash($username))) {
		Staff::setCookie($username, $ip);
		session_start();
		$_SESSION['result'] = "auth_success";
		session_write_close();
	} else {
		session_start();
		$_SESSION['result'] = "auth_error";
		session_write_close();
	}

	header('Location: ../modcp');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Mod CP</title>
	<link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="../css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="../css/main.css" type="text/css">
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
</head>
<body>
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
					echo '<li><a href="../clans/'.strtolower(Users::getClan()).'">My Clan</a></li>';
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
	<div class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<?=$result?>
		</div>
	</div>
	<?php
	if (loginController::isLoggedin() == "true" && Staff::isLoggedin() == "true") {
		count(ClanRequests::showAll()) == 0 ? $clanrequests = "<td>None</td><td></td><td></td><td></td>" : $clanrequests = "";
		foreach (ClanRequests::showAll() as $cr) {
			$clanrequests .= '<form action="" method="POST">';
			$clanrequests .= '<input type="hidden" name="id" value="'.$cr[0].'">';
			$clanrequests .= '<tr><td style="vertical-align: middle"><a href="'.$cr[3].'" target="_blank">'.$cr[1].'</a></td><td style="vertical-align: middle">'.$cr[2].'</td><td><input type="submit" class="btn btn-xs btn-success" name="create" value="Create"></td><td><input type="submit" class="btn btn-xs btn-danger" name="delete" value="Delete"></td></tr>';
			$clanrequests .= '</form>';
		}

		count(Contact::showAll()) == 0 ? $messages = "<td>None</td><td></td><td></td><td></td>" : $messages = "";
		foreach (Contact::showAll() as $m) {
			if ($m[2] != null) {
				strpos($m[2], '@') !== false ? $message_link = '<a href="mailto:'.$m[2].'">'.$m[1].'</a>' : $message_link = '<a href="http://forums.xgenstudios.com/private.php?do=newpm&u='.$m[2].'" target="_blank">'.$m[1].'</a>';
			} else {
				$message_link = $m[1];
			}
			$messages .= '<form action="" method="POST">';
			$messages .= '<input type="hidden" name="mid" value="'.$m[0].'">';
			$messages .= '<tr><td style="vertical-align: top">'.$message_link.'</td><td style="vertical-align: top">'.$m[3].'</td><td>'.nl2br($m[4]).'</td><td><input type="submit" class="btn btn-xs btn-danger" name="mdelete" value="Delete"></td></tr>';
			$messages .= '</form>';
		}
		echo '
	<div class="row">
		<div class="col-md-5 col-md-offset-3-5">
			<h2>Clan Requests</h2>
		</div>
	</div>
	<div class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<table class="table table-striped table-hover">
				<tr>
					<th width="40%">Clan name</th>
					<th width="40%">Leader</th>
					<th width="10%"></th>
					<th width="10%"></th>
				</tr>
				'.$clanrequests.'
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5 col-md-offset-3-5">
			<h2>Messages</h2>
		</div>
	</div>
	<div class="row top-buffer">
		<div class="col-md-5 col-md-offset-3-5">
			<table class="table table-striped table-hover">
				<tr>
					<th width="20%">Sender</th>
					<th width="20%">Category</th>
					<th width="50%">Message</th>
					<th width="10%"></th>
				</tr>
				'.$messages.'
			</table>
		</div>
	</div>
		';
	} else if (loginController::isLoggedin() == "true" && Staff::isLoggedin() == "false") {
		echo '
	<div class="row">
		<div class="col-md-3 col-md-offset-4">
			<h2>Mod CP Login</h2>
			<form action="" method="POST">
				<div class="form-group">
					<label>Username</label>
					<input class="form-control" name="username" placeholder="Username" type="text">
				</div>
				<div class="form-group">
					<label>Password</label>
					<input class="form-control" name="password" placeholder="Password" type="password">
				</div>
				<input class="btn btn-primary" type="submit" name="submit" value="Sign in">
			</form>
		</div>
	</div>';
	}
	?>
</div>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>