<?php
require_once("../connect.php");

$clan = $_GET['name'];

if (isset($_POST['scoreCurl'])) {

	if ($result = $mysqli->query("SELECT * FROM statmonitor WHERE attacker='$clan' OR defender='$clan'")) {
		$row = $result->fetch_assoc();
		$data['a_wins'] = $row['a_wins'];
		$data['d_wins'] = $row['d_wins'];
		exit(json_encode($data));
	} else {
		echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	$result->close();
}