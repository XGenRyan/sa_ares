<?php
require_once("connect.php");
require_once("controllers.php");
require_once("classes.php");

class Users
{
	public function escape($unsanitized)
	{
		global $mysqli;
		$sanitized = $mysqli->real_escape_string($unsanitized);
		return $sanitized;
	}

	public function changeName($userID, $username)
	{
		global $mysqli;
		$userID = Users::escape($userID);
		$username = strtolower(Users::escape($username));
		$username = ucfirst($username);

		if ($result = $mysqli->query("SELECT username FROM users WHERE uid='$userID'")) {
			$username_on_record = $result->fetch_row();
			$username_on_record = $username_on_record[0];
			if ($username != $username_on_record) {
				$mysqli->query("UPDATE users SET username='$username' WHERE uid='$userID'");
				$mysqli->query("UPDATE clanmembers SET username='$username' WHERE username='$username_on_record'");
				$mysqli->query("UPDATE clanrequests SET leader='$username' WHERE leader='$username_on_record'");
				$mysqli->query("UPDATE clans SET leader='$username' WHERE leader='$username_on_record'");
				$mysqli->query("UPDATE joinrequests SET username='$username' WHERE username='$username_on_record'");
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		$result->close();
	}
	
	public function isNewUser($user = null)
	{
		global $mysqli;
		if ($user == null) {
			$username = Users::escape(loginController::getUsername());
		} else {
			$username = Users::escape($user);
		}

		if ($result = $mysqli->query("SELECT * FROM users WHERE username='$username'")) {
			if ($result->num_rows == 0) {
				return "true";
			} else {
				return "false";
			}
			$result->close();
		}
	}

	public function addUser()
	{
		global $mysqli;
		global $username;
		global $email;
		global $secq;
		global $seca;
		global $userID;

		$username = Users::escape($username);
		$email = Users::escape($email);
		$secq = Users::escape($secq);
		$seca = Users::escape($seca);
		$userID = Users::escape($userID);
		$reg_ip = getenv('HTTP_CLIENT_IP')?:
		getenv('HTTP_X_FORWARDED_FOR')?:
		getenv('HTTP_X_FORWARDED')?:
		getenv('HTTP_FORWARDED_FOR')?:
		getenv('HTTP_FORWARDED')?:
		getenv('REMOTE_ADDR');
		$reg_date = date("Y-m-d H:i:s");

		if (Users::isNewUser() == "true") {
			if (!$mysqli->query("INSERT INTO users (uid, username, email, sec_question, sec_answer, reg_ip, reg_date) VALUES ('$userID', '$username', '$email', '$secq', '$seca', '$reg_ip', '$reg_date');")) {
				echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
	}

	public function updateLastLogin()
	{
		global $mysqli;
		global $username;

		$current_ip = getenv('HTTP_CLIENT_IP')?:
		getenv('HTTP_X_FORWARDED_FOR')?:
		getenv('HTTP_X_FORWARDED')?:
		getenv('HTTP_FORWARDED_FOR')?:
		getenv('HTTP_FORWARDED')?:
		getenv('REMOTE_ADDR');
		$last_login = date("Y-m-d H:i:s");

		if (!$mysqli->query("UPDATE users SET current_ip='$current_ip', last_login='$last_login' WHERE username='$username'")) {
			echo "Update failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateEmailAddress()
	{
		global $mysqli;
		global $username;
		global $newemail;
		global $seca;

		$username = Users::escape($username);
		$newemail = Users::escape($newemail);

		if (!$mysqli->query("UPDATE users SET email='$newemail' WHERE username='$username'")) {
			echo "Update failed: (" . $mysqli->errno . ") " . $mysqli->error;
		} else {
			session_start();
			$_SESSION['status'] = "update";
			session_write_close();
		}
	}

	public function updateSecurity()
	{
		global $mysqli;
		global $username;
		global $secq;
		global $seca;

		$username = Users::escape($username);
		$secq = Users::escape($secq);
		$seca = Users::escape($seca);

		if (!$mysqli->query("UPDATE users SET sec_question='$secq', sec_answer='$seca' WHERE username='$username'")) {
			echo "Update failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateClan($username, $clanname)
	{
		global $mysqli;
		$username = Users::escape($username);
		$clanname = Users::escape($clanname);

		if (!$mysqli->query("UPDATE users SET clan='$clanname' WHERE username='$username'")) {
			echo "Update failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getSecurityQuestion()
	{
		global $mysqli;
		$username = Users::escape(loginController::getUsername());

		if ($result = $mysqli->query("SELECT sec_question FROM users WHERE username='$username'")) {
			$sec_question = $result->fetch_row();
			return $sec_question[0];
		}
		$result->close();
	}

	public function getSecurityAnswer()
	{
		global $mysqli;
		$username = Users::escape(loginController::getUsername());

		if ($result = $mysqli->query("SELECT sec_answer FROM users WHERE username='$username'")) {
			$sec_answer = $result->fetch_row();
			return $sec_answer[0];
		}
		$result->close();
	}

	public function getEmailAddress()
	{
		global $mysqli;
		$username = Users::escape(loginController::getUsername());

		if ($result = $mysqli->query("SELECT email FROM users WHERE username='$username'")) {
			$email_address = $result->fetch_row();
			return $email_address[0];
		}
		$result->close();
	}

	public function hasClan($user = null)
	{
		global $mysqli;
		if ($user == null) {
			$username = Users::escape(loginController::getUsername());
		} else {
			$username = Users::escape($user);
		}

		if ($result = $mysqli->query("SELECT clan FROM users WHERE username='$username'")) {
			$clan = $result->fetch_row();
			$clan = $clan[0];
			
			if ($clan != "no") {
				return "true";
			} else {
				return "false";
			}
		}
		$result->close();
	}

	public function getClan($user = null)
	{
		global $mysqli;
		if ($user == null) {
			$username = Users::escape(loginController::getUsername());
		} else {
			$username = $user;
		}

		if ($result = $mysqli->query("SELECT clan FROM users WHERE username='$username'")) {
			$clan = $result->fetch_row();
			$clan = $clan[0];
			return $clan;
		}
		$result->close();
	}
}

class Clans
{
	public function getClanList()
	{
		global $mysqli;
		
		if (!$result = $mysqli->query("SELECT name FROM clans ORDER BY points DESC, id DESC")) {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		} else {
			$clan_array = array();
			while($row = $result->fetch_assoc()) {
				$clan_array[] = $row['name'];
			}
			return $clan_array;
		}

		$result->close();
	}

	public function json_getClanList()
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT * FROM clans ORDER BY points DESC")) {
			if ($result->num_rows > 0) {
				$json_clan_array = "";
				for ($i = 0; $i < $result->num_rows; ++$i) {
					$data = $result->fetch_assoc();
					$data["rank"] = $i + 1;
					unset($data['id']);
					$json_data = json_encode($data);
					$json_data = str_replace('"name"', 'name', $json_data);
					$json_data = str_replace('"members"', 'members', $json_data);
					$json_data = str_replace('"logo"', 'logo', $json_data);
					$json_data = str_replace('}', '},', $json_data);
					$json_clan_array .= $json_data;
				}
				$json_clan_array = '['.$json_clan_array.'];';
				return $json_clan_array;
			}
			$result->close();
		}
	}

	public function json_getClanDetails($clanname)
	{
		global $mysqli;
		$clanname = Users::escape($clanname);

		if ($result = $mysqli->query("SELECT * FROM clans WHERE name='$clanname' ORDER BY points DESC")) {
			$data = $result->fetch_assoc();
			unset($data['id']);
			$json_data = json_encode($data);
			$json_data = str_replace('"name"', 'name', $json_data);
			$json_data = str_replace('"members"', 'members', $json_data);
			$json_data = str_replace('"wins"', 'wins', $json_data);
			$json_data = str_replace('"losses"', 'losses', $json_data);
			$json_data = str_replace('"logo"', 'logo', $json_data);
			$json_data = str_replace('"website"', 'website', $json_data);
			$json_data = str_replace('"motto"', 'motto', $json_data);
			$json_data = str_replace('"creation_date"', 'date', $json_data);
			$json_data = str_replace('}', '},', $json_data);
			$json_clan_data = '['.$json_data.'];';
			return $json_clan_data;
		}
		$result->close();
	}

	public function isFieldEmpty($field, $clanname)
	{
		global $mysqli;
		$field = Users::escape($field);
		$clanname = Users::escape($clanname);

		if ($result = $mysqli->query("SELECT * FROM clans WHERE name='$clanname'")) {
			$data = $result->fetch_assoc();
			if ($data[$field] == '' || strlen($data[$field]) < 4) {
				return "true";
			} else {
				return "false";
			}
		}
		$result->close();
	}

	public function isLeader()
	{
		global $mysqli;
		$username = Users::escape(loginController::getUsername());

		if ($result = $mysqli->query("SELECT * FROM clans WHERE leader='$username'")) {
			if ($result->num_rows == 0) {
				return "false";
			} else {
				return "true";
			}
		}
		$result->close();
	}

	public function getRank($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT name FROM clans ORDER BY points DESC, id ASC")) {
			for ($i = 0; $i < $result->num_rows; ++$i) {
				$data = $result->fetch_assoc();
				if ($data['name'] == $clan) {
					return $i + 1;
					break;
				}
			}
			$result->close();
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getNumMembers($clan) {
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT members FROM clans WHERE name='$clan'")) {
			$data = $result->fetch_assoc();
			return $data['members'];
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getWins($clanname)
	{
		global $mysqli;
		$clanname = Users::escape($clanname);

		if ($result = $mysqli->query("SELECT wins FROM clans WHERE name='$clanname'")) {
			$data = $result->fetch_assoc();
			return $data['wins'];
		}
		$result->close();
	}

	public function getPoints($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT points FROM clans WHERE name='$clan'")) {
			$data = $result->fetch_assoc();
			return $data['points'];
		}
		$result->close();
	}

	public function numMembers()
	{
		global $mysqli;
		$username = Users::escape(loginController::getUsername());

		if ($result = $mysqli->query("SELECT members FROM clans WHERE leader='$username'")) {
			$members = $result->fetch_row();
			return $members[0];
		}
		$result->close();
	}

	public function getLogo($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT logo FROM clans WHERE name='$clan'")) {
			$logo = $result->fetch_row();
			return $logo[0];
		}
		$result->close();
	}

	public function updateField($fieldname, $input, $clan)
	{
		global $mysqli;
		$input = Users::escape($input);
		$clan = Users::escape($clan);

		if ($mysqli->query("UPDATE clans SET $fieldname='$input' WHERE name='$clan'")) {
			$mysqli->close();
			session_start();
			$_SESSION['status'] = "updated";
			session_write_close();
		} else {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updatePostWar($clan, $points, $matchresult)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($matchresult == "win") {
			if (!$result = $mysqli->query("UPDATE clans SET points='$points', wins=wins+1 WHERE name='$clan'")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		} else if ($matchresult == "loss") {
			if (!$result = $mysqli->query("UPDATE clans SET points='$points', losses=losses+1 WHERE name='$clan'")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
	}

	public function deleteClan($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if (!$result = $mysqli->query("DELETE FROM clans WHERE name='$clan'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$result = $mysqli->query("UPDATE users SET clan='no' WHERE clan='$clan'")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if ($result = $mysqli->query("SELECT * FROM joinrequests WHERE clan='$clan'")) {
			if ($result->num_rows > 0) {
				$mysqli->query("DELETE FROM joinrequests WHERE clan='$clan'");
			}
		}

		if ($result = $mysqli->query("SELECT * FROM clanmembers WHERE clan='$clan'")) {
			if ($result->num_rows > 0) {
				$mysqli->query("DELETE FROM clanmembers WHERE clan='$clan'");
			}
		}
		
		$result->close();
	}

	public function createClan($clanname, $leader, $website)
	{
		global $mysqli;
		$clanname = Users::escape($clanname);
		$leader = Users::escape($leader);
		$website = Users::escape($website);
		$creation_date = new DateTime();
		$creation_date = $creation_date->format('Y-m-d');

		if (!$mysqli->query("INSERT INTO clans (name, leader, website, creation_date) VALUES ('$clanname', '$leader', '$website', '$creation_date');")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function transferLeader($newleader, $clan)
	{
		global $mysqli;
		$oldleader = Users::escape(loginController::getUsername());
		$newleader = Users::escape($newleader);
		$clan = Users::escape($clan);

		if (!$result = $mysqli->query("UPDATE users SET clan='no' WHERE username='$oldleader'")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$result = $mysqli->query("UPDATE clans SET leader='$newleader' WHERE name='$clan'")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$result = $mysqli->query("UPDATE clans SET members=members-1 WHERE name='$clan'")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$result = $mysqli->query("DELETE FROM clanmembers WHERE username='$newleader'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getLeader($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT leader FROM clans WHERE name='$clan'")) {
			$leader = $result->fetch_assoc();
			$result->close();
			return $leader['leader'];
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class Cookies
{
	public function setCookie($username)
	{
		global $mysqli;
		$username = Users::escape($username);
		$key = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(sha1(time()), 1);

		if ($result = $mysqli->query("SELECT * FROM cookies WHERE username='$username'")) {
			if ($result->num_rows == 0) {
				$mysqli->query("INSERT INTO cookies (username, cookie) VALUES ('$username', '$key')");
			} else {
				$mysqli->query("UPDATE cookies SET cookie='$key' WHERE username='$username'");
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		setcookie('ARES', $key, time()+7200);
	}

	public function getUsername($cookie)
	{
		global $mysqli;
		$cookie = Users::escape($cookie);

		if ($result = $mysqli->query("SELECT username FROM cookies WHERE cookie='$cookie'")) {
			$data = $result->fetch_assoc();
			$result->close();
			return ucfirst(strtolower($data['username']));
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function destroyCookie($username)
	{
		global $mysqli;
		$username = Users::escape($username);

		if (!$result = $mysqli->query("DELETE FROM cookies WHERE username='$username'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class ClanRequests
{
	public function createClan()
	{
		global $mysqli;
		global $clanname;
		global $username;
		global $website;

		$clanname = Users::escape($clanname);
		$username = Users::escape($username);
		$website = Users::escape($website);

		if (!$mysqli->query("INSERT INTO clanrequests (name, leader, website) VALUES ('$clanname', '$username', '$website');")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			$mysqli->close();
		}
	}

	public function inProgress()
	{
		global $mysqli;
		$username = Users::escape(loginController::getUsername());

		if ($result = $mysqli->query("SELECT * FROM clanrequests WHERE leader='$username'")) {
			if ($result->num_rows > 0) {
				return "true";
			} else {
				return "false";
			}
			$result->close();
		}
	}

	public function showAll()
	{
		global $mysqli;

		$requests = array();
		if ($result = $mysqli->query("SELECT * FROM clanrequests")) {
			for ($i = 0; $i < $result->num_rows; $i++) {
				$requests[$i] = $result->fetch_row();
			}
			return $requests;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getRequest($id)
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT * FROM clanrequests WHERE id='$id'")) {
			$request = $result->fetch_row();
			return $request;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function deleteRequest($id)
	{
		global $mysqli;
		$id = Users::escape($id);
		
		if (!$result = $mysqli->query("DELETE FROM clanrequests WHERE id='$id'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class ResetQuestion
{
	public function sendReset()
	{
		global $mysqli;

		$username = Users::escape(loginController::getUsername());
		$email = Users::getEmailAddress();
		$to = $email;
		$string = "Emma Stone owns ".$username;
		$key = str_shuffle($string);
		$skey = md5($key);

		if ($result = $mysqli->query("SELECT * FROM resetquestion WHERE username='$username'")) {
			if ($result->num_rows == 0) {
				$mysqli->query("INSERT INTO resetquestion (username, email, skey) VALUES ('$username', '$email', '$skey');");
			} else {
				$mysqli->query("UPDATE resetquestion SET email='$email', skey='$skey' WHERE username='$username'");
			}

			try {
				//ares.targex@gmail.com pw EmMAStonE331
				$mandrill = new Mandrill('mC-8hgDj9Ag4oG3SkO19PA');
				$message = array(
					'html' => 'Hello '.$username.',<br />Click <a href="'.$_ENV['email_reset_url'].$skey.'">here</a> to reset your security question.',
					'subject' => 'Ares - Reset Security Question',
					'from_email' => 'ares.targex@gmail.com',
					'from_name' => 'Targex',
					'to' => array(
						array(
							'email' => $to,
							'type' => 'to'
						)
					),
					'headers' => array('Reply-To' => 'ares.targex@gmail.com'),
					'important' => false,
					'track_opens' => true,
					'track_clicks' => true,
					'tags' => array('ares, security reset')
				);
				$async = false;
				$result = $mandrill->messages->send($message, $async);
				session_start();
				$_SESSION['status'] = "reset";
				session_write_close();
			} catch (Mandrill_Error $e) {
				echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
				throw $e;
			}
		}
	}

	public function doesKeyExist()
	{
		global $mysqli;
		global $key;

		if ($result = $mysqli->query("SELECT * FROM resetquestion WHERE skey='$key'")) {
			if ($result->num_rows == 0) {
				return "false";
			} else {
				return "true";
			}
		}
		$result->close();
	}

	public function getUsername()
	{
		global $mysqli;
		global $key;

		if ($result = $mysqli->query("SELECT username FROM resetquestion WHERE skey='$key'")) {
			$username = $result->fetch_row();
			return $username[0];
		}
		$result->close();
	}

	public function deleteEntry()
	{
		global $mysqli;
		global $key;

		if (!$mysqli->query("DELETE FROM resetquestion WHERE skey='$key'")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class JoinRequests
{
	public function requestClan()
	{
		global $mysqli;
		global $clan;

		$username = Users::escape(loginController::getUsername());
		$clan = Users::escape($clan);

		if (!$mysqli->query("INSERT INTO joinrequests (username, clan) VALUES ('$username', '$clan');")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function inProgress()
	{
		global $mysqli;
		global $clan;

		$username = Users::escape(loginController::getUsername());
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT * FROM joinrequests WHERE username='$username' AND clan='$clan'")) {
			if ($result->num_rows > 0) {
				return "true";
			} else {
				return "false";
			}
		}
		$result->close();
	}

	public function json_getJoinRequests()
	{
		global $mysqli;
		$clan = Users::escape(Users::getClan());

		if ($result = $mysqli->query("SELECT * FROM joinrequests WHERE clan='$clan'")) {
			if ($result->num_rows > 0) {
				$json_join_requests = "";
				for ($i = 0; $i < $result->num_rows; ++$i) {
					$data = $result->fetch_assoc();
					unset($data['id'], $data['clan']);
					$json_data = json_encode($data);
					$json_data = str_replace('"username"', 'username', $json_data);
					$json_data = str_replace('}', '},', $json_data);
					$json_join_requests .= $json_data;
				}
				$json_join_requests = '['.$json_join_requests.'];';
				return $json_join_requests;
			}
		}
		$result->close();
	}

	public function deleteRequest($username)
	{
		global $mysqli;
		$username = Users::escape($username);
		$clan = Users::escape(Users::getClan());

		if (!$result = $mysqli->query("DELETE FROM joinrequests WHERE username='$username' AND clan='$clan'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		$result->close();
	}

	public function deleteAllRequests($username)
	{
		global $mysqli;
		$username = Users::escape($username);

		if (!$result = $mysqli->query("DELETE FROM joinrequests WHERE username='$username'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class ClanMembers
{
	public function json_getMemberList($clanname)
	{
		global $mysqli;
		$clanname = Users::escape($clanname);

		if (!$result = $mysqli->query("SELECT * FROM clanmembers WHERE clan='$clanname' ORDER BY username ASC")) {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		} else {
			if ($result->num_rows > 0) {
				$json_clan_members = "";
				for ($i = 0; $i < $result->num_rows; ++$i) {
					$data = $result->fetch_assoc();
					unset($data['id'], $data['clan']);
					$json_data = json_encode($data);
					$json_data = str_replace('"username"', 'username', $json_data);
					$json_data = str_replace('}', '},', $json_data);
					$json_clan_members .= $json_data;
				}
				$json_clan_members = '['.$json_clan_members.'];';
				return $json_clan_members;
			}
		}
		$result->close();
	}

	public function getMembers($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT username FROM clanmembers WHERE clan='$clan' ORDER BY username ASC")) {
			$members = array();
			for ($i = 0; $i < $result->num_rows; ++$i) {
				$member = $result->fetch_assoc();
				$members[] = $member['username'];
			}
			$result->close();
			return $members;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function addMember($username, $clanname)
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT clan FROM users WHERE username='$username'")) {
			$clan = $result->fetch_row();
			$clan = $clan[0];
			
			if ($clan == "no") {
				if (!$result = $mysqli->query("INSERT INTO clanmembers (username, clan) VALUES ('$username', '$clanname')")) {
					echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$result = $mysqli->query("UPDATE clans SET members=members+1 WHERE name='$clanname'")) {
					echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$result = $mysqli->query("UPDATE users SET clan='$clanname' WHERE username='$username'")) {
					echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
			}

			if (!$result = $mysqli->query("SELECT * FROM clanrequests WHERE leader='$username'")) {
				echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			} else {
				if ($result->num_rows > 0) {
					$mysqli->query("DELETE FROM clanrequests WHERE leader='$username'");
				}
			}

			if (!$result = $mysqli->query("DELETE FROM joinrequests WHERE username='$username'")) {
				echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
		$result->close();
	}

	public function removeMember($username, $clanname)
	{
		global $mysqli;


		if ($result = $mysqli->query("SELECT clan FROM users WHERE username='$username'")) {
			$clan = $result->fetch_row();
			$clan = $clan[0];
			
			if ($clan == $clanname) {
				if (!$result = $mysqli->query("UPDATE users SET clan='no' WHERE username='$username'")) {
					echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$result = $mysqli->query("DELETE FROM clanmembers WHERE username='$username'")) {
					echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}

				if (!$result = $mysqli->query("UPDATE clans SET members=members-1 WHERE name='$clanname'")) {
					echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
			}
		}
		$result->close();
	}

	public function isaMember($username)
	{
		global $mysqli;
		$username = Users::escape($username);

		if ($result = $mysqli->query("SELECT * FROM clanmembers WHERE username='$username'")) {
			if ($result->num_rows == 1) {
				return "true";
			} else {
				return "false";
			}
		}
	}
}

class WarDeclares
{
	public function submitDeclare()
	{
		global $mysqli;
		global $attacker;
		$attacker = Users::escape($attacker);
		global $defender;
		$defender = Users::escape($defender);
		global $type;
		global $rounds;
		global $map;
		global $tac;
		global $fks;
		global $guns;
		global $melee;
		global $hammercamps;
		global $running;
		global $stalling;
		global $podcamps;
		global $taor;

		if (!$result = $mysqli->query("INSERT INTO wardeclares (attacker, defender, type, rounds, map, tac, fks, guns, melee, hammercamps, running, stalling, podcamps, taor) VALUES ('$attacker', '$defender', '$type', '$rounds', '$map', '$tac', '$fks', '$guns', '$melee', '$hammercamps', '$running', '$stalling', '$podcamps', '$taor')")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function inProgress($clan)
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT * FROM wardeclares WHERE attacker='$clan' OR defender='$clan'")) {
			if ($result->num_rows > 0) {
				return "true";
			} else {
				return "false";
			}
		} else {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getEverything($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT * FROM wardeclares WHERE attacker='$clan' OR defender='$clan'")) {
			$declare = $result->fetch_row();
			return $declare;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getRounds($attacker, $defender)
	{
		global $mysqli;
		$attacker = Users::escape($attacker);
		$defender = Users::escape($defender);

		if ($result = $mysqli->query("SELECT rounds FROM wardeclares WHERE attacker='$attacker' AND defender='$defender'")) {
			$data = $result->fetch_assoc();
			return $data['rounds'];
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function deleteDeclare($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if (!$result = $mysqli->query("DELETE FROM wardeclares WHERE attacker='$clan' OR defender='$clan'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getTAC($clan)
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT tac FROM wardeclares WHERE defender='$clan'")) {
			$tac = $result->fetch_row();
			return $tac;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getType($clan)
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT type FROM wardeclares WHERE defender='$clan'")) {
			$type = $result->fetch_row();
			return $type;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class Queue
{
	public function inProgress($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT * FROM queue WHERE sender='$clan' OR recipient='$clan'")) {
			if ($result->num_rows > 0) {
				return "true";
			} else {
				return "false";
			}
		} else {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function newMessageExists($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT sender_read FROM queue WHERE sender='$clan'")) {
			if ($result->num_rows > 0) {
				$sender_read = $result->fetch_row();
				$sender_read = $sender_read[0];

				if ($sender_read == NULL || strtotime($sender_read) < self::getLastUpdatedTime($clan)) {
					return "true";
				} else if ($sender_read != NULL && strtotime($sender_read) > self::getLastUpdatedTime($clan)) {
					return "false";
				}
			} else if ($result->num_rows == 0) {
				$result2 = $mysqli->query("SELECT recipient_read FROM queue WHERE recipient='$clan'");
				if ($result2->num_rows > 0) {
					$recipient_read = $result2->fetch_row();
					$recipient_read = $recipient_read[0];

					if ($recipient_read == NULL || strtotime($recipient_read) < self::getLastUpdatedTime($clan)) {
						return "true";
					} else if ($recipient_read != NULL && strtotime($recipient_read) > self::getLastUpdatedTime($clan)) {
						return "false";
					}
				}
			} else {
				return "false";
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getLastUpdatedTime($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT last_updated FROM queue WHERE sender='$clan' OR recipient='$clan'")) {
			$last_updated = $result->fetch_row();
			$last_updated = strtotime($last_updated[0]);
			return $last_updated;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateLastRead($clan, $time)
	{
		global $mysqli;
		$clan = Users::escape($clan);
		$time = Users::escape($time);

		if ($result = $mysqli->query("SELECT * FROM queue WHERE sender='$clan'")) {
			if ($result->num_rows > 0) {
				$mysqli->query("UPDATE queue SET sender_read='$time' WHERE sender='$clan'");
			} else if ($result->num_rows == 0) {
				$mysqli->query("UPDATE queue SET recipient_read='$time' WHERE recipient='$clan'");
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function newQueue($sender, $recipient, $action)
	{
		global $mysqli;
		$sender = Users::escape($sender);
		$recipient = Users::escape($recipient);

		if (!$result = $mysqli->query("INSERT INTO queue (sender, recipient, action) VALUES ('$sender', '$recipient', '$action')")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateAction($clan, $action)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		$time = new DateTime();
		$time = $time->format('Y-m-d H:i:s');

		if (!$result = $mysqli->query("UPDATE queue SET action='$action', last_updated='$time' WHERE sender='$clan' OR recipient='$clan'")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getAction($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT action FROM queue WHERE sender='$clan' OR recipient='$clan'")) {
			$action = $result->fetch_row();
			$action = $action[0];
			return $action;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function isRecipient($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT * FROM queue WHERE recipient='$clan'")) {
			if ($result->num_rows == 0) {
				return "false";
			} else {
				return "true";
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getRecipient($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT recipient FROM queue WHERE sender='$clan'")) {
			$recipient = $result->fetch_row();
			$recipient = $recipient[0];
			return $recipient;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function getSender($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT sender FROM queue WHERE recipient='$clan'")) {
			$sender = $result->fetch_row();
			$sender = $sender[0];
			return $sender;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function deleteQueue($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if (!$result = $mysqli->query("DELETE FROM queue WHERE sender='$clan' OR recipient='$clan'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class StatMonitor
{
	public function addEntry()
	{
		global $mysqli;
		global $attacker;
		global $defender;
		global $d_p1;
		global $d_p2;

		$attacker = Users::escape($attacker);
		$defender = Users::escape($defender);

		$tac = WarDeclares::getTAC($defender);
		$created_at = date("Y-m-d H:i:s");

		if ($find = $mysqli->query("SELECT * FROM statmonitor WHERE attacker='$attacker' AND defender='$defender'")) {
			if ($find->num_rows == 0) {
				if (!$result = $mysqli->prepare("INSERT INTO statmonitor (attacker, defender, d_p1, d_p2, tac, created_at) VALUES ('$attacker', '$defender', ?, ?, '$tac', '$created_at')")) {
					echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
				} else {
					$result->bind_param('ss', $d_p1, $d_p2);
					$result->execute();
				}
			}
		}
	}

	public function deleteEntry($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($find = $mysqli->query("SELECT * FROM statmonitor WHERE attacker='$clan' OR defender='$clan'")) {
			if ($find->num_rows > 0) {
				if (!$result = $mysqli->query("DELETE FROM statmonitor WHERE attacker='$clan' OR defender='$clan'")) {
					echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				}
			}
		}
	}

	public function updatePlayers($clan)
	{
		global $mysqli;
		global $a_p1;
		global $a_p2;

		$clan = Users::escape($clan);

		if ($find = $mysqli->query("SELECT * FROM statmonitor WHERE attacker='$clan'")) {
			if ($find->num_rows > 0) {
				if (!$result = $mysqli->prepare("UPDATE statmonitor SET a_p1=?, a_p2=? WHERE attacker='$clan'")) {
					echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
				} else {
					$result->bind_param('ss', $a_p1, $a_p2);
					$result->execute();
				}
			}
		} 
	}

	public function updateReady($clan)
	{
		global $mysqli;
		global $declare;
		global $statmonitor;

		$clan = Users::escape($clan);

		if ($declare['attacker'] == $clan) {
			if (!$result = $mysqli->query("UPDATE statmonitor SET a_ready=1 WHERE attacker='$clan'")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}

		if ($declare['defender'] == $clan) {
			if (!$result = $mysqli->query("UPDATE statmonitor SET d_ready=1 WHERE defender='$clan'")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}

		if ($result = $mysqli->query("SELECT * FROM statmonitor WHERE attacker='$clan' OR defender='$clan'")) {
			$row = $result->fetch_assoc();
			$a_ready = $row['a_ready'];
			$d_ready = $row['d_ready'];
			
			if ($a_ready == 1 && $d_ready == 1) {

				// $players[] = $statmonitor['a_p1'];
				// if ($statmonitor['a_p2'] != NULL) $players[] = $statmonitor['a_p2'];
				// $players[] = $statmonitor['d_p1'];
				// if ($statmonitor['d_p2'] != NULL) $players[] = $statmonitor['d_p2'];

				//$stats = statController::getStats($players);

				// if (count($stats) == 2) {
				// 	$a_p1_deaths = explode(' ', $stats[0])[3];
				// 	$d_p1_deaths = explode(' ', $stats[1])[3];

				// 	$mysqli->query("UPDATE statmonitor SET a_p1_deaths='$a_p1_deaths', d_p1_deaths='$d_p1_deaths' WHERE attacker='$clan' OR defender='$clan'");
				// } else if (count($stats) == 4) {
				// 	$a_p1_deaths = explode(' ', $stats[0])[3];
				// 	$a_p2_deaths = explode(' ', $stats[1])[3];
				// 	$d_p1_deaths = explode(' ', $stats[2])[3];
				// 	$d_p2_deaths = explode(' ', $stats[3])[3];

				// 	$mysqli->query("UPDATE statmonitor SET a_p1_deaths='$a_p1_deaths', a_p2_deaths='$a_p2_deaths', d_p1_deaths='$d_p1_deaths', d_p2_deaths='$d_p2_deaths' WHERE attacker='$clan' OR defender='$clan'");
				// }

				$players_string = $statmonitor['a_p1'].' ';
				if ($statmonitor['a_p2'] != NULL) $players_string .= $statmonitor['a_p2'].' ';
				$players_string .= $statmonitor['d_p1'];
				if ($statmonitor['d_p2'] != NULL) $players_string .= ' '.$statmonitor['d_p2'];

				$job = new Jobs;
				$declare['tac'] == 1 ? $job->push("TAC_watchStats", $players_string) : $job->push("XGEN_watchStats", $players_string);

				Queue::updateAction($clan, "started");
				$mysqli->query("UPDATE statmonitor SET started=1 WHERE attacker='$clan' OR defender='$clan'");
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateWins($role, $clan, $wins)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($role == "attacker") {
			if (!$result = $mysqli->query("UPDATE statmonitor SET a_wins='$wins' WHERE attacker='$clan'")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		} else if ($role == "defender") {
			if (!$result = $mysqli->query("UPDATE statmonitor SET d_wins='$wins' WHERE defender='$clan'")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
	}

	public function getEverything($clan)
	{
		global $mysqli;
		$clan = Users::escape($clan);

		if ($result = $mysqli->query("SELECT * FROM statmonitor WHERE attacker='$clan' OR defender='$clan'")) {
			$statmonitor = $result->fetch_row();
			return $statmonitor;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class WarLogs
{
	public function addLog($attacker, $defender, $a_p1, $d_p1, $a_p2 = null, $d_p2 = null) {
		global $mysqli;

		if ($a_p2 == null || $d_p2 == null) {
			if (!$result = $mysqli->query("INSERT INTO warlogs (attacker, defender, a_p1, d_p1) VALUES ('$attacker', '$defender', '$a_p1', '$d_p1')")) {
				echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		} else {
			if (!$result = $mysqli->query("INSERT INTO warlogs (attacker, defender, a_p1, a_p2, d_p1, d_p2) VALUES ('$attacker', '$defender', '$a_p1', '$a_p2', '$d_p1', '$d_p2')")) {
				echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
	}

	public function updateWinsLog($attacker, $defender, $a_wins, $d_wins, $completed)
	{
		global $mysqli;

		if (!$result = $mysqli->query("UPDATE warlogs SET a_wins='$a_wins', d_wins='$d_wins', completed='$completed' WHERE attacker='$attacker' AND defender='$defender' ORDER BY id DESC LIMIT 1")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateDeathLog($attacker, $defender, $a_deaths, $d_deaths) {
		global $mysqli;

		if (!$result = $mysqli->query("UPDATE warlogs SET a_deaths=a_deaths+'$a_deaths', d_deaths=d_deaths+'$d_deaths' WHERE attacker='$attacker' AND defender='$defender' ORDER BY id DESC LIMIT 1")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateStartingRatingLog($attacker, $defender, $a_rating, $d_rating) {
		global $mysqli;

		if (!$result = $mysqli->query("UPDATE warlogs SET a_starting_points='$a_rating', d_starting_points='$d_rating' WHERE attacker='$attacker' AND defender='$defender' ORDER BY id DESC LIMIT 1")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updatePointDifference($attacker, $defender, $a_diff, $d_diff) {
		global $mysqli;

		if (!$result = $mysqli->query("UPDATE warlogs SET a_point_diff='$a_diff', d_point_diff='$d_diff' WHERE attacker='$attacker' AND defender='$defender' ORDER BY id DESC LIMIT 1")) {
			echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function updateRulesandSettings($attacker, $defender) {
		global $mysqli;

		if ($result = $mysqli->query("SELECT type, rounds, map, tac, fks, guns, melee, hammercamps, running, stalling, podcamps, taor FROM wardeclares WHERE attacker='$attacker' AND defender='$defender'")) {
			$settings = array();
			$keys = array("type", "rounds", "map", "tac", "fks", "guns", "melee", "hammercamps", "running", "stalling", "podcamps", "taor");
			$row = $result->fetch_assoc();
			foreach ($keys as $k) {
				$settings[] = $row[$k];
			}
			if (!$result = $mysqli->query("UPDATE warlogs SET type='$settings[0]', rounds='$settings[1]', map='$settings[2]', tac='$settings[3]', fks='$settings[4]', guns='$settings[5]', melee='$settings[6]', hammercamps='$settings[7]', running='$settings[8]', stalling='$settings[9]', podcamps='$settings[10]', taor='$settings[11]' WHERE attacker='$attacker' AND defender='$defender' ORDER BY id DESC LIMIT 1")) {
				echo "UPDATE failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class TempIDs
{
	public function addID($id, $username)
	{
		global $mysqli;
		$id = Users::escape($id);
		$username = Users::escape($username);

		if ($result = $mysqli->query("SELECT * FROM tempids WHERE user_id='$id'")) {
			if ($result->num_rows == 0) {
				$mysqli->query("INSERT INTO tempids (user_id, username) VALUES ('$id', '$username')");
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function deleteID($id, $username = null)
	{
		global $mysqli;
		$id = Users::escape($id);
		$username = Users::escape($username);

		if ($username == null) {
			if ($result = $mysqli->query("SELECT * FROM tempids WHERE user_id='$id'")) {
				if ($result->num_rows > 0) {
					$mysqli->query("DELETE FROM tempids WHERE user_id='$id'");
				}
			} else {
				echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		} else {
			if ($result = $mysqli->query("SELECT * FROM tempids WHERE username='$username'")) {
				if ($result->num_rows > 0) {
					$mysqli->query("DELETE FROM tempids WHERE username='$username'");
				}
			} else {
				echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
	}

	public function getAllIDs()
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT user_id FROM tempids")) {
			$id_array = array();
			while($row = $result->fetch_assoc()) {
				$id_array[] = $row['user_id'];
			}
			return $id_array;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		$result->close();
	}
}

class Contact
{
	public function sendMessage($sender, $email, $topic, $message, $sender_ip)
	{
		global $mysqli;

		if (!$result = $mysqli->query("INSERT INTO contact (sender, email, category, message, sender_ip) VALUES ('$sender', '$email', '$topic', '$message', '$sender_ip')")) {
			echo "INSERT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function showAll()
	{
		global $mysqli;

		$messages = array();
		if ($result = $mysqli->query("SELECT * FROM contact")) {
			for ($i = 0; $i < $result->num_rows; $i++) {
				$messages[$i] = $result->fetch_row();
			}
			return $messages;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function deleteMessage($mid)
	{
		global $mysqli;
		$mid = Users::escape($mid);

		if (!$result = $mysqli->query("DELETE FROM contact WHERE id='$mid'")) {
			echo "DELETE failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}
}

class Staff
{
	public function isLoggedin()
	{
		if (self::getUsername() == "") {
			return "false";
		}
		
		if (isset($_COOKIE['ARES_staff'])) {
			return "true";
		} else {
			return "false";
		}
	}

	public function getUsername()
	{
		global $mysqli;
		isset($_COOKIE['ARES_staff']) ? $cookie = Users::escape($_COOKIE['ARES_staff']) : $cookie = "";

		if ($cookie != "") {
			if ($result = $mysqli->query("SELECT username FROM staff WHERE cookie='$cookie'")) {
				$data = $result->fetch_assoc();
				$result->close();
				return ucfirst(strtolower($data['username']));
			} else {
				echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
		}
	}

	public function getStaffMembers()
	{
		global $mysqli;

		if ($result = $mysqli->query("SELECT username FROM staff")) {
			$username = array();
			while($row = $result->fetch_assoc()) {
				$username[] = $row['username'];
			}
			return $username;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		$result->close();
	}

	public function getHash($username) {
		global $mysqli;
		$username = Users::escape($username);

		if ($result = $mysqli->query("SELECT password FROM staff WHERE username='$username'")) {
			$data = $result->fetch_assoc();
			$hash = $data['password'];
			return $hash;
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}

	public function setCookie($username, $ip) {
		global $mysqli;
		$username = Users::escape($username);
		$ip = Users::escape($ip);
		$key = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1).substr(sha1(time()), 1);

		if ($result = $mysqli->query("SELECT * FROM staff WHERE username='$username'")) {
			if ($result->num_rows > 0) {
				$mysqli->query("UPDATE staff SET cookie='$key', current_ip='$ip' WHERE username='$username'");
			}
		} else {
			echo "SELECT failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		setcookie('ARES_staff', $key, time()+3600);
	}
}