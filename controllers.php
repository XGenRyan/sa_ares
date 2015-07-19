<?php
require_once("models.php");
require_once("classes.php");

class loginController
{
	public function APIauth()
	{
		global $authSuccess;
		global $userID;
		global $username;
		global $password;
		$password = trim($password);
		$xml = simplexml_load_file("http://api.xgenstudios.com/?method=xgen.users.authenticate&username=".$username."&password=".$password);

		if ($xml['stat'] == "ok") {
			$authSuccess = "true";
			$userID = $xml->user['id'];
		} else {
			$authSuccess = "false";
		}
	}

	public function getCookie()
	{
		return $_COOKIE['ARES'];
	}

	public function setCookie()
	{
		global $username;
		Cookies::setCookie($username);
	}

	public function destroyCookie()
	{
		$username = self::getUsername();
		unset($_COOKIE['ARES']);
		setcookie('ARES', '', time() - 7200);

		Cookies::destroyCookie($username);

		if (isset($_COOKIE['ARES_UID'])) {
			TempIDs::deleteID($_COOKIE['ARES_UID']);
			unset($_COOKIE['ARES_UID']);
			setcookie('ARES_UID', '', time() - 3600);
		}

		if (isset($_COOKIE['ARES_staff'])) {
			unset($_COOKIE['ARES_staff']);
			setcookie('ARES_staff', '', time() - 3600);
		}
	}

	public function redirectUser()
	{
		if (loginController::isLoggedin() == "true") {
			header('Location: ./');
		}
	}

	public function isLoggedin()
	{
		if (self::getUsername() == "") {
			return "false";
		}
		
		if (isset($_COOKIE['ARES'])) {
			return "true";
		} else {
			return "false";
		}
	}

	public function getUsername()
	{
		if (isset($_COOKIE['ARES'])) return Cookies::getUsername($_COOKIE['ARES']);
	}
}

class registerController
{
	public function redirectUser()
	{
		if (loginController::isLoggedin() == "true" && Users::isNewUser() == "true") {
			header('Location: register');
		}
	}
}

class warController
{
	public function eraseWar($clan = null)
	{
		if ($clan == null) {
			$clan = Users::getClan();

			if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false" || Queue::inProgress($clan) == "false") {
				header('Location: ./');
			}
		}

		WarDeclares::deleteDeclare($clan);
		Queue::deleteQueue($clan);
		StatMonitor::deleteEntry($clan);
	}

	public function arePlayersValid($p1, $p2)
	{
		global $clan;
		$clan = Users::escape($clan);

		$members = ClanMembers::getMembers($clan);
		$members[] = Clans::getLeader($clan);

		if ($p2 == NULL) {
			if (in_array($p1, $members)) return "true";
			return "false";
		} else {
			if (in_array($p1, $members) && in_array($p2, $members)) return "true";
			return "false";
		}
	}

	public function acceptWar()
	{
		global $attacker;
		global $defender;
		$action = "selection";

		$clan = Users::getClan();

		if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false" || Queue::inProgress($clan) == "false") {
			header('Location: ./');
		}

		Queue::deleteQueue($clan);
		StatMonitor::addEntry();
		Queue::newQueue($defender, $attacker, $action);
	}

	public function updateWar()
	{
		global $attacker;
		global $defender;
		$action = "ready";

		$clan = Users::getClan();

		if (loginController::isLoggedin() == "false" || Users::hasClan() == "false" || Clans::isLeader() == "false" || Queue::inProgress($clan) == "false") {
			header('Location: ./');
		}

		Queue::deleteQueue($clan);
		StatMonitor::updatePlayers($clan);
		Queue::newQueue($attacker, $defender, $action);
	}

	public function endWar($winner, $attacker, $defender)
	{
		if ($winner == $attacker) {
			$Attacking = new Glicko2Clan($rating = Clans::getPoints($attacker));
			$Defending = new Glicko2Clan($rating = Clans::getPoints($defender));

			$a_original_rating = $Attacking->rating;
			$d_original_rating = $Defending->rating;

			WarLogs::updateStartingRatingLog($attacker, $defender, $a_original_rating, $d_original_rating);

			$Attacking->AddWin($Defending);
			$Defending->AddLoss($Attacking);

			$Attacking->Update();
			$Defending->Update();

			$a_new_rating = $Attacking->rating;
			$d_new_rating = $Defending->rating;

			$a_original_rating > $a_new_rating ? $a_point_diff = $a_original_rating - $a_new_rating : $a_point_diff = $a_new_rating - $a_original_rating;
			$d_original_rating > $d_new_rating ? $d_point_diff = $d_original_rating - $d_new_rating : $d_point_diff = $d_new_rating - $d_original_rating;

			WarLogs::updatePointDifference($attacker, $defender, $a_point_diff, $d_point_diff);

			Clans::updatePostWar($attacker, $Attacking->rating, "win");
			Clans::updatePostWar($defender, $Defending->rating, "loss");
		} else if ($winner == $defender) {
			$Defending = new Glicko2Clan($rating = Clans::getPoints($defender));
			$Attacking = new Glicko2Clan($rating = Clans::getPoints($attacker));

			$Defending->AddWin($Attacking);
			$Attacking->AddLoss($Defending);

			$Defending->Update();
			$Attacking->Update();

			Clans::updatePostWar($defender, $Defending->rating, "win");
			Clans::updatePostWar($attacker, $Attacking->rating, "loss");
		}

		WarDeclares::deleteDeclare($attacker);
		Queue::deleteQueue($attacker);
		StatMonitor::deleteEntry($attacker);
	}
}

class staffController
{
	public function createClan($id, $leader, $clanname, $website)
	{
		JoinRequests::deleteAllRequests($leader);
		Users::updateClan($leader, $clanname);
		Clans::createClan($clanname, $leader, $website);
		ClanRequests::deleteRequest($id);
	}

	public function deleteRequest($id)
	{
		ClanRequests::deleteRequest($id);
	}
}