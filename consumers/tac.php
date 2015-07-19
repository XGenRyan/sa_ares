<?php
require_once("../classes.php");

$job = new Jobs;
$job->executeTAC("TAC_watchStats");