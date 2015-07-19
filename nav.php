<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="./">Ares</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-left">
				<?php
				if (loginController::isLoggedin() == "true" && Users::hasClan() == "true" && Users::isNewUser() == "false") {
					echo '<li><a href="clans/'.strtolower(Users::getClan()).'">My Clan</a></li>';
				}

				if (loginController::isLoggedin() == "true" && Users::hasClan() == "true" && Clans::isLeader() == "true") {
					echo '<li><a href="manage">Manage Clan</a></li>';
				}

				if (loginController::isLoggedin() == "true" && Users::hasClan() == "true" && Clans::isLeader() == "true" && Queue::inProgress(Users::getClan()) == "true") {
					if (Queue::newMessageExists(Users::getClan()) == "true") {
						$badge = ' <span class="badge">new</span>';
					} else {
						$badge = "";
					}
					echo '<li><a href="queue">Queue'.$badge.'</a></li>';
				}
				
				if (loginController::isLoggedin() == "true") {
					echo '<li><a href="contact">Contact</a></li>';
				}
				?>
				<li><a href="docs">Docs</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<?php
			if (in_array(loginController::getUsername(), Staff::getStaffMembers())) {
				$modcp = '<li><a href="modcp">Mod CP</a></li><li class="divider"></li>';
			} else {
				$modcp= "";
			}
			if (loginController::isLoggedin() == "false") { 
				echo '<li><a href="login">Login</a></li>'; 
			} else {
				echo '<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.loginController::getUsername().' <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						'.$modcp.'
						<li><a href="update">Update E-mail Address</a></li>
						<li><a href="reset">Reset Security Questions</a></li>
						<li class="divider"></li>
						<li><a href="logout">Logout</a></li>
					</ul>
				</li>';
			}
			?>
			</ul>
		</div>
	</div>
</nav>