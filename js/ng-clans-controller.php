<?php
require_once(dirname(__DIR__)."/models.php");
$clan_details = Clans::json_getClanDetails($clan);
$clan_members = ClanMembers::json_getMemberList($clan);
?>
<script>
var main = angular.module('main', []);

main.controller('ClanDetails', function ($scope) {
	$scope.clans = <?=$clan_details?>
	$scope.members = <?=$clan_members?>
	$scope.parseInt = parseInt;
});
</script>