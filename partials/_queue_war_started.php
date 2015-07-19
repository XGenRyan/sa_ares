<?php

if (Queue::getAction($clan) == "started") {
	echo '
	<div class="col-md-8 col-md-offset-2">
		<div class="jumbotron">
			<h2>War has begun!</h2>
			<p>Keep checking back here frequently to see how the games between '.$matchup.' are progressing in real time. Remember to take screenshots of the matches (or record them) so you have plenty evidence to back up your claims!</p>
			<p>'.$statmonitor['attacker'].': <span id="attacker_score">'.$statmonitor['a_wins'].'</span></p>
			<p>'.$statmonitor['defender'].': <span id="defender_score">'.$statmonitor['d_wins'].'</span></p>
		</div>
	</div>';

	echo '
	<script>
	setInterval(function() {
		$.post("./curls/checkMatchProgress.php?name='.$clan.'", { scoreCurl: true }, function(data) {
			data.a_wins = parseInt(data.a_wins);
			data.d_wins = parseInt(data.d_wins);

			if (isNaN(data.a_wins) || isNaN(data.d_wins)) {
				document.location.href = "./?war=done";
			}
			
			if (parseInt($("#attacker_score").html()) != data.a_wins) {
				$("#attacker_score").html(data.a_wins);
			}

			if (parseInt($("#defender_score").html()) != data.d_wins) {
				$("#defender_score").html(data.d_wins);
			}
		},"json");
	},2000);
	</script>
	';
}