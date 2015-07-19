<?php

if ($clan == $declare['attacker']) {
	$monitoring = "a_ready";
} else {
	$monitoring = "d_ready";
}

if (isset($statmonitor['a_p1']) && isset($statmonitor['d_p1'])) {
	if ($statmonitor['a_p2'] != NULL && $statmonitor['d_p2'] != NULL) {
		$matchup = $statmonitor['a_p1'].' &amp; '.$statmonitor['a_p2'].' vs. '.$statmonitor['d_p1'].' &amp; '.$statmonitor['d_p2'];
	} else {
		$matchup = $statmonitor['a_p1'].' vs. '.$statmonitor['d_p1'];
	}
}

if (Queue::getAction($clan) == "ready" && $statmonitor[$monitoring] == 0) {

	echo '
	<form action="queue" method="POST" onsubmit="return confirm(\'Are you sure you want to continue?\')">
		<div class="col-md-8 col-md-offset-2">
			<div class="jumbotron">
				<h2>'.$matchup.'</h2>
				<p>Ready to start!</p>
				<p>Whenever you are ready to begin press the ready button. Once both parties are ready the clan war will officially begin and everyones\' stats will be monitored.</p>
				<p style="font-size: .95em"><b>Note</b>: We strongly recommend checking to make sure all of the players are in the server ready to play before continuing.</p>
				<p>
					<input name="ready" class="btn btn-success btn-lg" type="submit" value="Ready">
					<input name="cancel" class="btn btn-danger btn-lg" type="submit" value="Cancel War">
				</p>
			</div>
		</div>
	</form>';
} else if (Queue::getAction($clan) == "ready" && $statmonitor[$monitoring] == 1 && $statmonitor['started'] == 0) {
	echo '
	<form action="queue" method="POST" onsubmit="return confirm(\'Are you sure you want to cancel this war? Everything you did up to this point will be undone.\')">
		<div class="col-md-8 col-md-offset-2">
			<div class="jumbotron">
				<h2>Waiting...</h2>
				<p>Looks like the other clan isn\'t ready yet. No worries though, once they have readied up we will let you know. Just make sure to keep this page open.</p>
				<p>Tired of waiting?</p>
				<p>
					<input name="cancel" class="btn btn-danger btn-lg" type="submit" value="Cancel War">
				</p>
			</div>
		</div>
	</form>';

	echo '
	<script>
	setInterval(function(){
		$.post("./curls/checkIfReady.php?name='.$clan.'", { readyCurl: true }, function(data){
			if(data.started == 1) {
				setTimeout(function() {
					location.reload();
				}, 1500);
			}
		},"json");
	},1000);
	</script>
	';
}