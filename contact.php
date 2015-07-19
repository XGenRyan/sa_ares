<?php
require_once("models.php");
require_once("controllers.php");

if (loginController::isLoggedin() == "false") {
	header('Location: ./login');
}

if (isset($_POST['submit'])) {
	$sender = Users::escape(loginController::getUsername());
	$sender_ip = $_SERVER['REMOTE_ADDR'];
	$email = Users::escape($_POST['email']);
	$topic = Users::escape($_POST['topic']);
	$message = Users::escape($_POST['message']);

	switch ($topic) {
		case 2:
			$topic = "War Discrepancy";
			break;
		case 3:
			$topic = "Bugs/Glitches";
			break;
		case 4:
			$topic = "Suggestions";
			break;
		default:
			$topic = "General";
	}

	Contact::sendMessage($sender, $email, $topic, $message, $sender_ip);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Contact</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row">
		<div id="result" class="col-md-4 col-md-push-4">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<h2>Contact</h2>
			<div class="form-group">
				<label>Topic</label>
				<select name="topic" class="form-control">
					<option value="1">General</option>
					<option value="2">War Discrepancy</option>
					<option value="3">Bugs/Glitches</option>
					<option value="4">Suggestions</option>
				</select>
			</div>
			<div class="form-group">
				<label>E-mail Address (or XGen Forums username)</label>
				<input id="email" class="form-control" type="email" name="email" placeholder="Optional">
			</div>
			<div class="form-group">
				<label>Message</label>
				<textarea class="form-control" name="message" rows="12"></textarea>
			</div>
			<button id="send" class="btn btn-primary" name="submit"><i class="glyphicon glyphicon-send"></i> Send</button>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script>
$("select").change(function() {
	switch (parseInt($("select").val())) {
		case 2:
			$("textarea").attr("placeholder", "Any sort of war-related arguments go here, whether it be someone breaking the nfk rule or someone leaving the game last second. Make sure to include screenshots or video evidence if you have any hope of something being done about it.\n\nThis is also the place to report clans for poor sportsmanship/unfair play.");
			break;
		case 3:
			$("textarea").attr("placeholder", "Report any sort of website or war-related system glitches you encounter here. Please be as descriptive as possible and include what page(s) it occurred on. If there is an error message, copy and paste that here.\n\nJust to be clear, this topic is ONLY for glitches. Do not tell us about any petty war squabbles (like someone free killing you) - go to War Discepancies for that.");
			break;
		case 4:
			$("textarea").attr("placeholder", "Feel free to send in any ideas you have. There's no guarantee that they will be implemented, but they will at least be considered.");
			break;
		default:
			$("textarea").attr("placeholder", "");
	}
});

var submit = false;

$("textarea").change(function() {
	if ($("textarea").val().length > 10) {
		submit = true;
	}
});

$("#send").click(function() {
	var topic = $("option").val();
	var email = $("#email").val();
	var message = $("textarea").val();

	if (submit) {
		$.ajax({
			type: "POST",
			data: { 'submit': 'submit', 'topic': topic, 'email': email, 'message': message },
			url: "contact",
			dataType: "html",
			success: function(response) {
				$("#result").html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Success!</strong> Your message was sent.</div>');
				$("#send").attr("disabled", "disabled");
			}
		});
	}
});
</script>
</body>
</html>