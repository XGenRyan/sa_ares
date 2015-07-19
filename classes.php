<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once("models.php");
require_once("controllers.php");

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Jobs
{
	protected $username;
	protected $password;
	protected $host;
	protected $port;

	const timeout = 3600;

	public function __construct($username = "guest", $password = "guest", $host = "localhost", $port = 5672)
	{
		$this->username = $username;
		$this->password = $password;
		$this->host = $host;
		$this->port = $port;
	}

	public function push($job_name, $job_msg)
	{
		$this->job_name = $job_name;
		$connection = new AMQPConnection($this->host, $this->port, $this->username, $this->password);
		$channel = $connection->channel();
		$channel->queue_declare($this->job_name, false, true, false, false);
		$msg = new AMQPMessage($job_msg);
		$channel->basic_publish($msg, '', $this->job_name);
		$channel->close();
		$connection->close();
	}

	public function executeTAC($job_name)
	{
		$connection = new AMQPConnection($this->host, $this->port, $this->username, $this->password);
		$channel = $connection->channel();
		list($queue, $msg_cnt, $consumer_cnt) = $channel->queue_declare($job_name, true, true, false, false);

		if ($msg_cnt > 0) {

			$callback = function($msg) {
				$players = explode(" ", $msg->body);

				if (count($players) == 4) {
					$attacker = Users::getClan($players[0]);
					$defender = Users::getClan($players[2]);
					WarLogs::addLog($attacker, $defender, $players[0], $players[2], $players[1], $players[3]);
				} else {
					$attacker = Users::getClan($players[0]);
					$defender = Users::getClan($players[1]);
					WarLogs::addLog($attacker, $defender, $players[0], $players[1]);
				}

				$a_wins = 0;
				$d_wins = 0;
				$win_cap = (int)substr(WarDeclares::getRounds($attacker, $defender), 0, -2);

				$initial_time = array();
				$initial_deaths = array();
				for ($i = 0; $i < count($players); $i++) {
					$json = file_get_contents("http://tac.targex.org/api/?method=tac.latest.record.get&username=".$players[$i]); 
					$tac = json_decode($json);
					$tac == NULL ? $initial_time[] = 0 : $initial_time[] = strtotime($tac->created_at);
					$tac == NULL ? $initial_deaths[] = 0 : $initial_deaths[] = (int)$tac->deaths;
				}
				$startTime = time();
				do {
					if ($a_wins == $win_cap) {
						WarLogs::updateWinsLog($attacker, $defender, $a_wins, $d_wins, 1);
						WarLogs::updateRulesandSettings($attacker, $defender);
						warController::endWar($attacker, $attacker, $defender);
						exit();
					} else if ($d_wins == $win_cap) {
						WarLogs::updateWinsLog($attacker, $defender, $a_wins, $d_wins, 1);
						WarLogs::updateRulesandSettings($attacker, $defender);
						warController::endWar($defender, $attacker, $defender);
						exit();
					} else if (time() > $startTime + self::timeout) {
						WarLogs::updateWinsLog($attacker, $defender, $a_wins, $d_wins, 0);
						WarLogs::updateRulesandSettings($attacker, $defender);
						warController::eraseWar($attacker);
						exit();
					} else {
						if (!isset($current_time) && !isset($current_deaths)) {
							$current_time = array();
							$current_deaths = array();
						}
						for ($i = 0; $i < count($players); $i++) {
							$json = file_get_contents("http://tac.targex.org/api/?method=tac.latest.record.get&username=".$players[$i]); 
							$tac = json_decode($json);
							$tac == NULL ? $current_time[$i] = 0 : $current_time[$i] = strtotime($tac->created_at);
							//$tac == NULL ? $current_deaths[$i] = 0 : $current_deaths[$i] = (int)$tac->deaths;
						}
						if (count($players) == 4) {
							if ($initial_time[0] != $current_time[0] || $initial_time[1] != $current_time[1] || $initial_time[2] != $current_time[2] || $initial_time[3] != $current_time[3]) {

								sleep(6);

								for ($i = 0; $i < count($players); $i++) {
									$json = file_get_contents("http://tac.targex.org/api/?method=tac.latest.record.get&username=".$players[$i]);
									$tac = json_decode($json);
									$tac == NULL ? $current_deaths[$i] = 0 : $current_deaths[$i] = (int)$tac->deaths;
								}

								$attackers_deaths = $current_deaths[0] + $current_deaths[1];
								$defenders_deaths = $current_deaths[2] + $current_deaths[3];
								WarLogs::updateDeathLog($attacker, $defender, $attackers_deaths, $defenders_deaths);

								if ($attackers_deaths < $defenders_deaths) {
									++$a_wins;
									StatMonitor::updateWins("attacker", $attacker, $a_wins);
								} else if ($attackers_deaths > $defenders_deaths) {
									++$d_wins;
									StatMonitor::updateWins("defender", $defender, $d_wins);
								}

								$initial_time[0] = $current_time[0];
								$initial_deaths[0] = $current_deaths[0];
								$initial_time[1] = $current_time[1];
								$initial_deaths[1] = $current_deaths[1];
								$initial_time[2] = $current_time[2];
								$initial_deaths[2] = $current_deaths[2];
								$initial_time[3] = $current_time[3];
								$initial_deaths[3] = $current_deaths[3];
							}
						} else {
							if ($initial_time[0] != $current_time[0] || $initial_time[1] != $current_time[1]) {

								sleep(4);

								for ($i = 0; $i < count($players); $i++) {
									$json = file_get_contents("http://tac.targex.org/api/?method=tac.latest.record.get&username=".$players[$i]);
									$tac = json_decode($json);
									$tac == NULL ? $current_deaths[$i] = 0 : $current_deaths[$i] = (int)$tac->deaths;
								}

								$attackers_deaths = $current_deaths[0];
								$defenders_deaths = $current_deaths[1];
								WarLogs::updateDeathLog($attacker, $defender, $attackers_deaths, $defenders_deaths);

								if ($attackers_deaths < $defenders_deaths) {
									++$a_wins;
									StatMonitor::updateWins("attacker", $attacker, $a_wins);
								} else if ($attackers_deaths > $defenders_deaths) {
									++$d_wins;
									StatMonitor::updateWins("defender", $defender, $d_wins);
								}

								$initial_time[0] = $current_time[0];
								$initial_deaths[0] = $current_deaths[0];
								$initial_time[1] = $current_time[1];
								$initial_deaths[1] = $current_deaths[1];
							}
						}
					}
					sleep(6);
				} while (1 == 1);
			};

			$channel->basic_consume($job_name, '', false, true, false, false, $callback);
			while (count($channel->callbacks)) {
				$channel->wait();
			}

		} else {
			$channel->basic_cancel($job_name);
		}

		$channel->close();
		$connection->close();
	}

	public function executeXGEN($job_name)
	{
		$connection = new AMQPConnection($this->host, $this->port, $this->username, $this->password);
		$channel = $connection->channel();
		list($queue, $msg_cnt, $consumer_cnt) = $channel->queue_declare($job_name, true, true, false, false);

		if ($msg_cnt > 0) {

			$callback = function($msg) {
				$players = explode(" ", $msg->body);

				if (count($players) == 4) {
					$attacker = Users::getClan($players[0]);
					$defender = Users::getClan($players[2]);
					WarLogs::addLog($attacker, $defender, $players[0], $players[2], $players[1], $players[3]);
				} else {
					$attacker = Users::getClan($players[0]);
					$defender = Users::getClan($players[1]);
					WarLogs::addLog($attacker, $defender, $players[0], $players[1]);
				}

				$a_wins = 0;
				$d_wins = 0;
				$win_cap = (int)substr(WarDeclares::getRounds($attacker, $defender), 0, -2);
				
				$initial_stats = array();
				$initial_deaths = array();
				for ($i = 0; $i < count($players); $i++) {
					$xml = simplexml_load_file("http://api.xgenstudios.com/?method=xgen.stickarena.stats.get&username=".$players[$i]);
					$initial_stats[] = $xml->stats->game->user->stat[2].' '.$xml->stats->game->user->stat[3];
					$initial_deaths[] = (int)$xml->stats->game->user->stat[3];
				}
				$startTime = time();
				do {
					if ($a_wins == $win_cap) {
						WarLogs::updateWinsLog($attacker, $defender, $a_wins, $d_wins, 1, 0);
						WarLogs::updateRulesandSettings($attacker, $defender);
						warController::endWar($attacker, $attacker, $defender);
						exit();
					} else if ($d_wins == $win_cap) {
						WarLogs::updateWinsLog($attacker, $defender, $a_wins, $d_wins, 1, 0);
						WarLogs::updateRulesandSettings($attacker, $defender);
						warController::endWar($defender, $attacker, $defender);
						exit();
					} else if (time() > $startTime + self::timeout) {
						WarLogs::updateWinsLog($attacker, $defender, $a_wins, $d_wins, 0, 0);
						WarLogs::updateRulesandSettings($attacker, $defender);
						warController::eraseWar($attacker);
						exit();
					} else {
						if (!isset($current_stats) && !isset($current_deaths)) {
							$current_stats = array();
							$current_deaths = array();
						}
						for ($i = 0; $i < count($players); $i++) {
							$xml = simplexml_load_file("http://api.xgenstudios.com/?method=xgen.stickarena.stats.get&username=".$players[$i]);

							$current_stats[$i] = $xml->stats->game->user->stat[2].' '.$xml->stats->game->user->stat[3];
							//$current_deaths[$i] = (int)$xml->stats->game->user->stat[3];
						}
						if (count($players) == 4) {
							if ($initial_stats[0] != $current_stats[0] || $initial_stats[1] != $current_stats[1] || $initial_stats[2] != $current_stats[2] || $initial_stats[3] != $current_stats[3]) {

								sleep(4);

								for ($i = 0; $i < count($players); $i++) {
									$xml = simplexml_load_file("http://api.xgenstudios.com/?method=xgen.stickarena.stats.get&username=".$players[$i]);
									$current_deaths[$i] = (int)$xml->stats->game->user->stat[3];
								}

								$attackers_deaths = ($current_deaths[0] - $initial_deaths[0]) + ($current_deaths[1] - $initial_deaths[1]);
								$defenders_deaths = ($current_deaths[2] - $initial_deaths[2]) + ($current_deaths[3] - $initial_deaths[3]);
								WarLogs::updateDeathLog($attacker, $defender, $attackers_deaths, $defenders_deaths);

								if ($attackers_deaths < $defenders_deaths) {
									++$a_wins;
									StatMonitor::updateWins("attacker", $attacker, $a_wins);
								} else if ($attackers_deaths > $defenders_deaths) {
									++$d_wins;
									StatMonitor::updateWins("defender", $defender, $d_wins);
								}

								$initial_stats[0] = $current_stats[0];
								$initial_deaths[0] = $current_deaths[0];
								$initial_stats[1] = $current_stats[1];
								$initial_deaths[1] = $current_deaths[1];
								$initial_stats[2] = $current_stats[2];
								$initial_deaths[2] = $current_deaths[2];
								$initial_stats[3] = $current_stats[3];
								$initial_deaths[3] = $current_deaths[3];
							}
						} else {
							if ($initial_stats[0] != $current_stats[0] || $initial_stats[1] != $current_stats[1]) {

								sleep(2);

								for ($i = 0; $i < count($players); $i++) {
									$xml = simplexml_load_file("http://api.xgenstudios.com/?method=xgen.stickarena.stats.get&username=".$players[$i]);
									$current_deaths[$i] = (int)$xml->stats->game->user->stat[3];
								}
									
								$attackers_deaths = $current_deaths[0] - $initial_deaths[0];
								$defenders_deaths = $current_deaths[1] - $initial_deaths[1];
								WarLogs::updateDeathLog($attacker, $defender, $attackers_deaths, $defenders_deaths);

								if ($attackers_deaths < $defenders_deaths) {
									++$a_wins;
									StatMonitor::updateWins("attacker", $attacker, $a_wins);
								} else if ($attackers_deaths > $defenders_deaths) {
									++$d_wins;
									StatMonitor::updateWins("defender", $defender, $d_wins);
								}

								$initial_stats[0] = $current_stats[0];
								$initial_deaths[0] = $current_deaths[0];
								$initial_stats[1] = $current_stats[1];
								$initial_deaths[1] = $current_deaths[1];
							}
						}
					}
					sleep(3);
				} while (1 == 1);
			};

			$channel->basic_consume($job_name, '', false, true, false, false, $callback);
			while (count($channel->callbacks)) {
				$channel->wait();
			}

		} else {
			$channel->basic_cancel($job_name);
		}

		$channel->close();
		$connection->close();
	}
}

class Glicko2Clan
{
	public $rating;
	public $rd;
	public $sigma;

	public $mu;
	public $phi;
	public $tau;

	private $pi2 = 9.8696044;

	var $M = array();

	public function __construct($rating = 1500, $rd = 350, $volatility = 0.06, $mu = null, $phi = null, $sigma = null, $systemconstant = 0.3)
	{
		$this->rating = $rating;
		$this->rd = $rd;

		if (is_null($sigma)) {
			$this->sigma = $volatility;
		} else {
			$this->sigma = $sigma;
		}

		$this->tau = $systemconstant;

		if (is_null($mu)) {
			$this->mu = ( $this->rating - 1500 ) / 173.7178;
		} else {
			$this->mu = $mu;
		}

		if (is_null($phi)) {
			$this->phi = $this->rd / 173.7178;
		} else {
			$this->phi = $phi;
		}
	}

	public function AddWin($OtherPlayer)
	{
		$this->M[] = $OtherPlayer->MatchElement(1);
	}

	public function AddLoss($OtherPlayer)
	{
		$this->M[] = $OtherPlayer->MatchElement(0);
	}

	public function AddDraw($OtherPlayer)
	{
		$this->M[] = $OtherPlayer->MatchElement(0.5);
	}

	public function Update()
	{
		$Results = $this->AddMatches($this->M);
		$this->rating = $Results['r'];
		$this->rd = $Results['RD'];
		$this->mu = $Results['mu'];
		$this->phi = $Results['phi'];
		$this->sigma = $Results['sigma'];
		$this->M = array();
	}

	public function MatchElement($score)
	{
		return array( 'mu' => $this->mu, 'phi' => $this->phi, 'score' => $score );
	}

	public function AddMatches($M)
	{
		if (count($M) == 0) {
			$phi_p = sqrt( ( $this->phi * $this->phi ) + ( $this->sigma * $this->sigma ) );
			return array( 'r' => $this->rating, 'RD' => 173.7178 * $phi_p, 'mu' => $this->mu, 'phi' => $phi_p, 'sigma' => $this->sigma ) ;
		}

		$v_sum = 0;
		$delta_sum = 0;
		$mu_p_sum = 0;
		for ($j = 0; $j < count($M); $j++) {
			$E = $this->E( $this->mu, $M[$j]['mu'], $M[$j]['phi'] );
			$g = $this->g( $M[$j]['phi'] );
			$v_sum +=  ( $g * $g * $E * ( 1 - $E ) );

			$delta_sum += $g * ( $M[$j]['score'] - $E );

			$mu_p_sum += $g * ( $M[$j]['score'] - $E );
		}

		$v = 1.0 / $v_sum;

		$delta = $v * $delta_sum;

		$a = log( $this->sigma * $this->sigma );
		$x_prev = $a;
		$x = $x_prev;
		$tausq = $this->tau * $this->tau;
		$phisq = $this->phi * $this->phi;
		$deltasq = $delta * $delta;
		do {
			$exp_xp = exp( $x_prev );
			$d = $this->phi * $this->phi + $v + $exp_xp;
			$deltadsq = $deltasq / ($d * $d);
			$h1 = -( $x_prev - $a ) / ( $tausq ) - ( 0.5 * $exp_xp / $d ) + ( 0.5 * $exp_xp * $deltadsq );
			$h2 = ( -1.0 / $tausq ) - ( ( 0.5 * $exp_xp ) * ( $phisq + $v ) / ( $d * $d ) ) + ( 0.5 * $deltasq * $exp_xp * ( $phisq + $v - $exp_xp ) / ( $d * $d * $d ) );
			$tmp_x = $x;
			$x = $x_prev - ( $h1 / $h2 );
			$x_prev = $tmp_x;
		} while (abs($x - $x_prev) > 0.1);

		$sigma_p = exp( $x / 2 );

		$phi_star = sqrt( $phisq + ( $sigma_p * $sigma_p ) );

		$phi_p = 1.0 / ( sqrt( ( 1.0 / ( $phi_star * $phi_star ) ) + ( 1.0 / $v ) ) );

		$mu_p = $this->mu + $phi_p * $phi_p * $mu_p_sum;

		return array( 'r' => ( 173.7178 * $mu_p ) + 1500, 'RD' => 173.7178 * $phi_p, 'mu' => $mu_p, 'phi' => $phi_p, 'sigma' => $sigma_p );
	}

	public function g($phi)
	{
		return 1.0 / ( sqrt( 1.0 + ( 3.0 * $phi * $phi) / ( $this->pi2 ) ) );
	}

	public function E($mu, $mu_j, $phi_j)
	{
		return 1.0 / ( 1.0 + exp( -$this->g($phi_j) * ( $mu - $mu_j ) ) );
	}
}