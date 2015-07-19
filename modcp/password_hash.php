<?php
if (isset($_POST['submit'])) {
	if (strlen($_POST['password']) < 6) {
		echo '<font color="red">Error! Please choose a password that is longer than 6 characters.</font>';
	} else {
		echo '<font color="green">Paste this to Ryan: '.password_hash($_POST['password'], PASSWORD_DEFAULT).'</font>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Password Hasher</title>
</head>
<body>
<form action="" method="POST">
	<label>Password</label>
	<br />
	<input type="password" name="password" required>
	<br />
	<input type="submit" name="submit" value="Encrypt">
</form>
</body>
</html>