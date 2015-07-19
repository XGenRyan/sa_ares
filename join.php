<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

if (loginController::isLoggedin() == "false") {
	header('Location: login');
} else {
	$clan = $_POST['clan'];
	if (JoinRequests::inProgress() == "false") {
		JoinRequests::requestClan();
	}
}