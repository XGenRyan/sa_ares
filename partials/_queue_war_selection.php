<?php

if (Queue::getAction($clan) == "selection") {

	if ($declare['type'] == "1v1") {
		$phrase = $statmonitor['d_p1'];
		$phrase2 = "a player";
	} else {
		$phrase = $statmonitor['d_p1']." &amp; ".$statmonitor['d_p2'];
		$phrase2 = "players";
	}

	if (Queue::isRecipient($clan) == "true") {
		echo '
		<form action="queue" method="POST" onsubmit="return confirm(\'Are you sure you want to continue?\')">
			<div class="col-md-8 col-md-offset-2">
				<div class="jumbotron">
					<h2>Player Selection</h2>
					<p>'.Queue::getSender($clan).' has accepted the war and they have chosen '.$phrase.' to play for them. It is now your turn to choose who will play for '.$clan.':</p>
					</p>
					<p style="font-size: 1.4em">'.$roster.'</p>
					<p>
						<input name="lock" class="btn btn-success btn-lg" type="submit" value="Lock In">
						<input name="cancel" class="btn btn-danger btn-lg" type="submit" value="Cancel War">
					</p>
				</div>
			</div>
		</form>';
	} else {
		echo '
		<form action="queue" method="POST" onsubmit="return confirm(\'Are you sure you want to cancel this war?\')">
			<div class="col-md-8 col-md-offset-2">
				<div class="jumbotron">
					<h2>Waiting on '.Queue::getRecipient($clan).' to choose '.$phrase2.'...</h2>
					<p>If they are taking too long or you no longer wish to war you may cancel this war.</p>
					<p>
						<input name="cancel" class="btn btn-danger btn-lg" type="submit" value="Cancel War">
					</p>
				</div>
			</div>
		</form>';
	}
}