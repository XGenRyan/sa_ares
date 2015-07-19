<?php
require_once("models.php");
require_once("controllers.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ares - Documentation</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
	<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
<?php include "nav.php"; ?>
<div class="container">
	<div class="row top-buffer" style="margin-top: 20px">
		<div class="col-md-3 col-md-offset-1-4 docs-list">
			<ul>
				<li>Preface</li>
				<li><a href="" id="intro">Introduction</a></li>
				<li><a href="#why-use-this">Why use this?</a></li>
				<li><a href="#release-cycles">Release cycles</a></li>
				<li><a href="#changelog">Changelog</a></li>
			</ul>
			<ul>
				<li>Overview for</li>
				<li><a href="#new-users-overview">New users</a></li>
				<li><a href="#clan-leaders-overview">Clan leaders</a></li>
				<li><a href="#clan-members-overview">Clan members</a></li>
			</ul>
			<ul>
				<li>How-to</li>
				<li><a href="#how-to-make-a-clan">Make a clan</a></li>
				<li><a href="#how-to-join-a-clan">Join a clan</a></li>
				<li><a href="#how-to-war-a-clan">War a clan</a></li>
			</ul>
			<ul>
				<li>FAQ</li>
				<li><a href="#faq-login-system">Do you know what my password is?</a></li>
				<li><a href="#faq-war-process">What happens after a war is "started"?</a></li>
				<li><a href="#faq-clan-ranking">How are clans ranked?</a></li>
				<li><a href="#faq-join-button">Why did the "Join Clan" button vanish?</a></li>
				<li><a href="#faq-war-length">How come wars are only 2/3 or 3/5?</a></li>
				<li><a href="#faq-cheaters">The clan I was warring cheated, what do I do?</a></li>
				<li><a href="#faq-bugs">Where do I report bugs?</a></li>
			</ul>
		</div>
		<div class="col-md-6 docs-content">
			<section id="introduction">
				<h3>Introduction</h3>
				<p>Ares is an easy-to-maneuver site dedicated to Stick Arena that enables you to find and join clans, as well as create them.</p>
				<p>Warring has never been made easier. Set the rules of engagement and challenge other clans to matches as the score updates in real time using data obtained directly from the game.</p>
			</section>
			<section id="why-use-this">
				<h3>Why use this?</h3>
				<ol>
					<li>For the competitive aspect of climbing the clan leaderboard.</li>
					<li>Automate meaningless tasks like reporting scores and updating war records.</li>
					<li>Easily find clans to join.</li>
					<li>Clan management is a breeze.</li>
				</ol>
			</section>
			<section id="release-cycles">
				<h3>Release cycles</h3>
				<p>Ares releases new updates through a time-based model. What does that mean? Well, simply put, every six months (November and May) there will be an update with at least one new feature.</p>
			</section>
			<section id="changelog">
				<h3>Changelog</h3>
				<aside id="v1.0.1">
					<h4>Version 1.0.1</h4>
					<h5>October 10, 2014</h5>
					<p>Fixed bug that causes war records not to update properly after a war for certain clans.</p>
				</aside>
				<aside id="v1.0.0">
					<h4>Version 1.0.0</h4>
					<h5>October 1, 2014</h5>
					<p>Initial release.</p>
				</aside>
			</section>
			<section id="new-users-overview">
				<h3>New users</h3>
				<p>When you first login with your SA account you will be brought to a registration page. You will be asked to enter an e-mail address and to create a security question with an answer. It's important to use valid information so that you can recover your account should you lose/forget your information. Once you have signed up you can either create a clan or join an existing one - up to you.</p>
				<p><b>Caveat</b>: Staff members can see your security question, but not your security answer. Be mindful of that.</p>
			</section>
			<section id="clan-leaders-overview">
				<h3>Clan leaders</h3>
				<p>As a clan leader you have complete control over your clan. By visting the "<a href="manage" target="_blank">Manage Clan</a>" page you will be able to change your clan's logo, website URL, and motto - you will also be able to see join requests, kick players, etc.</p>
				<p>You can declare war on other clans by visiting their page. If a clan declares war on you, you will be notified.</p>
			</section>
			<section id="clan-members-overview">
				<h3>Clan members</h3>
				<p>As a clan member your options are currently limited to quitting the clan and being selected by the clan leader for warring. In future updates we plan on implementing a clan hierarchy (among other features) to keep clan members more involved.</p>
			</section>
			<section id="how-to-make-a-clan">
				<h3>Making a clan</h3>
				<p>You will need to apply for a clan and have it approved by a staff member. This is to prevent the creation of "alt clans". Approvals may take 1-2 days.</p>
				<p>Here are directions on how to apply for a clan:</p>
				<p>
					<ol>
						<li>Log in.</li>
						<li>Click the big green "Create Clan" button that's on the home page.</li>
						<li>Pick a name for your clan and keep in mind it must be between 3-16 characters and it cannot contain illegal characters (i.e., anything other than letters, numbers, periods, commas, underscores and spaces). Clan names containing foul language will not be accepted.</li>
						<li>Enter a website URL (or a xat chat URL) if applicable. If you don't have a website right now, just leave it blank. You can add one in at any time.</li>
						<li>Click the "Create" button.</li>
						<li>You will be redirected to the home page. If you see a "Pending..." button in place of the "Create Clan" button that means your application was successfully submitted but we haven't got around to checking it yet.</li>
						<li>Be patient. If you did everything correctly we'll approve your application.</li>
					</ol>
				</p>
				<p>If your application was approved you will see two new pages on your navigation bar - one called "My Clan" and one called "Manage Clan". If you were denied the "Pending..." button will revert back to "Create Clan".</p>
				<p>If your application was denied and you want to know why you can contact us by logging in and visiting the <a href="contact" target="_blank">contact page</a>.</p>
			</section>
			<section id="how-to-join-a-clan">
				<h3>Joining a clan</h3>
				<p>Similar to how users can't directly create clans, users can't directly join clans. By clicking the "Join Clan" button you are submitting a <i>request</i> to join that clan. The clan leader will see your request and either accept you into the clan or reject you.</p>
				<p>Here are directions on how to join a clan:</p>
				<p>
					<ol>
						<li>Log in.</li>
						<li>Visit a clan page.</li>
						<li>Click the "Join Clan" button.</li>
						<li>Confirm the join request.</li>
						<li>Done. It's that simple.</li>
						<li>Be patient. Clan leaders might not check their requests regularly. It could take awhile.</li>
					</ol>
				</p>
				<p>You can submit as many join requests to as many clans as you want simultaneously; however, once a clan accepts you, all of the subsequent join requests will be deleted.</p>
			</section>
			<section id="how-to-war-a-clan">
				<h3>Warring a clan</h3>
				<p>As a clan leader you are able to declare war on other clans. When you declare war on a clan, you will notice a new page called "Queue" will appear on the navigation bar. There will be a little badge next to the word "Queue" that says "new" if there is a new message that you have not read.</p>
				<p>The queue is essentially a back-and-forth communication stream between clan leaders. If you are the attacking clan the process looks like this:</p>
				<p>
					<ol>
						<li>Log in.</li>
						<li>Navigate to the clan you want to war's profile.</li>
						<li>Click the big green "Declare War" button.</li>
						<li>Choose the game settings and the rules.</li>
						<li>When ready, click the green "Continue" button.</li>
						<li>The queue page will say "Waiting on response from X...".</li>
						<li>If the clan has accepted the war, it will be indicated by the "new" badge on the queue page. You can see who they chose to play for them and it is now your turn to select players.</li>
						<li>Once you have selected your players, click the green "Lock in" button.</li>
						<li>The queue page will refresh and you will see a "ready up" message. Once everyone is in the lobby, click the "Ready" button.</li>
						<li>Once both clans are ready, the queue page will automatically refresh and the war will begin.</li>
						<li>Create the game according to the settings and make sure that everyone who's supposed to play is in the room.</li>
					</ol>
				</p>
				<p>On the other end:</p>
				<p>
					<ol>
						<li>Log in.</li>
						<li>You will see a page on your navigation bar called "Queue" with a badge that says "new" next to it. This means someone declared war on you. Click it.</li>
						<li>The queue will say "War declare from Y" and it will list all of the rules. Review them carefully.</li>
						<li>Select who will play for your clan.</li>
						<li>Click the green "Accept" button.</li>
						<li>The queue will refresh and you will see a message saying: "Waiting on response from Y...".</li>
						<li>Once the other clan has chosen who will play for them you will be notified by a new queue message badge.</li>
						<li>Click the queue page.</li>
						<li>On the queue page you will see a "ready up" message. Once everyone is in the lobby, click the "Ready" button.</li>
						<li>Once both clans are ready, the queue page will automatically refresh and the war will begin.</li>
						<li>Join the game.</li>
					</ol>
				</p>
				<p>At any time during this process either clan may cancel/decline the war. However, once the war has begun, it can not be stopped and both clans have <i>one</i> hour to complete it. If it is not completed, it will be deleted as if it never happened; clan records are not affected.</p>
				<p><b>Note</b>: We strongly recommend that both parties take screenshots right before the rounds end, or, if possible, record the entire series. Should the other clan cheat, you will have evidence to back up your claims.</p>
			</section>
			<section id="faq-login-system">
				<h3>Q: Do you know what my password is?</h3>
				<p>We don't. XGen Studios provides a service that checks the login credentials entered against their MMOcha account database. This is also how independent game developers such as Sean Cooper and Afroninja authenticate XGen accounts despite their games not necessarily being created by XGen.</p>
			</section>
			<section id="faq-war-process">
				<h3>Q: What happens after a war is "started"?</h3>
				<p>When a war is started either the XGen stats API or the TAC latest record API are continuously checked. When either API has changed, the deaths for the round are compared and the clan with the lowest amount of deaths wins the round. The win is then pushed to the queue page within a few seconds, so the leaders can see how the war is progressing in real time according to our system.</p>
				<p>Once the amount of rounds needed to win the war is reached, the war will end, both leaders will be redirected to the main page, and both clans' records will be updated accordingly.</p>
			</section>
			<section id="faq-clan-ranking">
				<h3>Q: How are clans ranked?</h3>
				<p>Clans are ranked by points. Points are calculated by using the Glicko-2 system, which is an improvement of the popular ELO rating system. To describe how this system works in the simplest of terms: you get a lot of points if you beat a clan that is ranked higher than yours, and you lose a lot of points if you lose to a clan that is ranked lower than yours. If you want to see the actual formula we use and read the full explanation you can do so here: <a href="http://www.glicko.net/glicko/glicko2.pdf" target="_blank">http://www.glicko.net/glicko/glicko2.pdf</a></p>
				<p>If you want to see how many points you will win/lose by warring a certain clan, you can view the point calculator <a href="calc" target="_blank">here</a>.</p>
			</section>
			<section id="faq-join-button">
				<h3>Q: Why did the "Join Clan" button vanish?</h3>
				<p>A: Once a clan reaches 15 members their join button will disappear. The join button will reappear once the member count is under 15.</p>
			</section>
			<section id="faq-war-length">
				<h3>Q: How come wars are only 2/3 or 3/5?</h3>
				<p>A: A lot of thought was put into potentially making this happen, but "screen wars" (wars that exceed 3/5, as in first to X wins where X is most commonly 10 or 20) are just not viable for us. Here's why:</p>
				<p>Whenever a war is started our server grabs data from either TAC's API or the XGen stats API. It continually does this until the war is finished (or until the hour timeout is reached). To give you an idea of what's happening behind the scenes, imagine you're refreshing the Targex Stat Sigs every second for each player in the war and you're making a note of their stats. If their stats change, you have to calculate the difference between them and determine who won. You then have to report the win/loss and keep doing this process until the amount of round wins needed to win the war is reached. Sounds pretty tiresome, right?</p>
				<p>It is, especially for our server. We're using up a lot of RAM by doing this. Our server can handle it without a performance loss for a short period of time (about an hour), which is why 2/3 and 3/5 are perfect (they can usually be finished within an hour). A proposed solution to this problem was implementing a pause/start button for a war.</p>
				<p>But what if a leader forgets to pause it? Well, I suppose that would be a fairly rare occurrence, so we could set up something to monitor our server's RAM and restart it if it gets too high. But then what if a leader decides to hit pause when he/she's about to lose, then the loss won't be recorded? Well, we could make it so both leaders have to agree to pause the war before it's actually paused. But what if one leader had to leave unexpectedly? Well...</p>
				<p>We could keep going. There's just too many variables involved. If anyone can think of a good plan that would account for all of these variables, we'd like to hear it.</p>
				<p><b>TL;DR</b>: For a lot of good semi-technical reasons.</p>
			</section>
			<section id="faq-cheaters">
				<h3>Q: The clan I was warring cheated, what do I do?</h3>
				<p>If the clan violated the war rules or left the game last minute to avoid a loss, visit the <a href="contact" target="_blank">contact page</a> and report it as a War Discrepancy. Make sure to include screenshots/video footage.</p>
			</section>
			<section id="faq-bugs">
				<h3>Q: Where do I report bugs?</h3>
				<p><a href="contact" target="_blank">Contact page</a>. Bugs/glitches.</p>
			</section>
		</div>
	</div>
</div>
<div id="up" class="circle">
	<i class="glyphicon glyphicon-chevron-up"></i>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script>
$("#up").hide();

$(window).on("scroll", function() {
	var y_scroll_pos = window.pageYOffset;
	var scroll_pos_test = 580;

	if (y_scroll_pos > scroll_pos_test) {
		$("#up").fadeIn("slow");
	} else {
		$("#up").fadeOut("slow");
	}
});

$("#up").click(function() {
	$("html, body").animate({ scrollTop: 0 }, "slow");
});

$("#intro").click(function() {
	$("#introduction").scrollTop();
});
</script>
</body>
</html>