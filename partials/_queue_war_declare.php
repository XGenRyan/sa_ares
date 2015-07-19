<?php

if (!empty(ClanMembers::getMembers($clan))) {
	$members = ClanMembers::getMembers($clan);
		array_unshift($members, Clans::getLeader($clan));
} else {
	$members[] = Clans::getLeader($clan);
}

if ($declare['tac'] == 1) {
	$declare['tac'] = "TAC is mandatory for every player.";
} else {
	$declare['tac'] = "All of the players must be in a <b>public</b> room -- the use of TAC is optional.";
}

$options = "";
foreach ($members as $m) {
	$options .= '<option value="'.$m.'">'.$m.'</option>';
}

$roster1 = '
		<div class="form-group">
			<label>Select Player</label>
			<select id="roster" name="player1" class="form-control" style="max-width: 250px">
				'.$options.'
			</select>
		</div>
		';

$roster2 = '
		<div class="form-group">
			<label>Select Players</label>
			<select id="roster" name="player1" class="form-control" style="max-width: 250px">
				'.$options.'
			</select>
		</div>
		<div class="form-group">
			<select id="roster2" name="player2" class="form-control" style="max-width: 250px">
				'.$options.'
			</select>
		</div>
		';

if ($declare['type'] == "1v1") {
	$roster = $roster1;
} else if ($declare['type'] == "2v2") {
	$roster = $roster2;
}

$rules = [];

if ($declare['fks'] == 0) {
	$rules[] = "<li>No freekills</li>";
}

if ($declare['guns'] == 0) {
	$rules[] = "<li>No guns</li>";
}

if ($declare['melee'] == 0) {
	$rules[] = "<li>No melee</li>";
}

if ($declare['hammercamps'] == 0) {
	$rules[] = "<li>No hammer camping</li>";
}

if ($declare['running'] == 0) {
	$rules[] = "<li>No running</li>";
}

if ($declare['stalling'] == 0) {
	$rules[] = "<li>No stalling</li>";
}

if ($declare['podcamps'] == 0) {
	$rules[] = "<li>No pod camps</li>";
}

if ($declare['taor'] == 1) {
	$rules[] = "<li>Type at your own risk</li>";
}

if (Queue::getAction($clan) == "declare") {

	if (Queue::isRecipient($clan) == "true") {
		echo '
		<form action="queue" method="POST" onsubmit="return confirm(\'Are you sure you want to proceed?\')">
			<div class="col-md-8 col-md-offset-2">
				<div class="jumbotron">
					<h2>War Declare from '.Queue::getSender($clan).'</h2>
					<p>'.Queue::getSender($clan).' declares a '.$declare['type'].' war on '.$clan.', best '.$declare['rounds'].'. All of the games will be played on the map '.$declare['map'].'. '.$declare['tac'].' The rules for this war include:
						</p>
					<p>
						<ul style="font-size: 1.4em">
							<li>No hacking</li>
							'.implode('', $rules).'
						</ul>
					</p>
					<p style="font-size: 1.4em">'.$roster.'</p>
					<p style="font-size: .95em"><b>Note</b>: We recommend contacting the leader of '.Queue::getSender($clan).' to discuss when and where this war will take place before you accept.</p>
					<p>
						<input name="accept" class="btn btn-success btn-lg" type="submit" value="Accept">
						<input name="decline" class="btn btn-danger btn-lg" type="submit" value="Decline">
					</p>
				</div>
			</div>
		</form>';
	} else {
		echo '
		<form action="queue" method="POST" onsubmit="return confirm(\'Are you sure you want to cancel this war?\')">
			<div class="col-md-8 col-md-offset-2">
				<div class="jumbotron">
					<h2>Waiting on response from '.Queue::getRecipient($clan).'...</h2>
					<p>If they are taking too long or you no longer wish to war you may cancel this war.</p>
					<p>
						<input name="cancel" class="btn btn-danger btn-lg" type="submit" value="Cancel War">
					</p>
				</div>
			</div>
		</form>';
	}
}