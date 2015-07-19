<?php
require_once("models.php");
require_once("controllers.php");

registerController::redirectUser();

if (loginController::isLoggedin() == "false" || $_POST['clan'] != Users::getClan() || Clans::isLeader() == "true") {
	header('Location: ./');
} else {
	$clan = Users::escape($_POST['clan']);
	$username = Users::escape(loginController::getUsername());
	
	$mysqli->query("DELETE FROM clanmembers WHERE username='$username' AND clan='$clan'");
	$mysqli->query("UPDATE clans SET members=members-1 WHERE name='$clan'");
	$mysqli->query("UPDATE users SET clan='no' WHERE username='$username'");
	$mysqli->close();
}