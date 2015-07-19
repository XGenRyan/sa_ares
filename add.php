<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false") {
	header('Location: ./');
} else {
	$username = Users::escape($_POST['username']);
	$clanname = Users::escape(Users::getClan());
	if (Clans::getNumMembers($clanname) < 15) {
		ClanMembers::addMember($username, $clanname);
	}
}