<?php
require_once("../connect.php");

$clan = $_GET['name'];

if (isset($_POST['readyCurl'])) {

	if ($result = $mysqli->query("SELECT started FROM statmonitor WHERE attacker='$clan' OR defender='$clan'")) {
		$row = $result->fetch_assoc();
		$data['started'] = $row['started'];
		exit(json_encode($data));
	} else {
		echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	$result->close();
}