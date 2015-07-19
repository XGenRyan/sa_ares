<?php
require_once("controllers.php");
loginController::destroyCookie();
session_start();
$_SESSION['status'] = "logout";
session_write_close();
header('Location: ./');