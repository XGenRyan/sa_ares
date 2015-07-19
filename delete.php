<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false") {
	header('Location: ./');
} else {
	$username = Users::escape($_POST['username']);
	JoinRequests::deleteRequest($username);
}