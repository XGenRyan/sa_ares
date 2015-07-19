<?php
require_once(dirname(__DIR__)."/models.php");
$clan_details = Clans::json_getClanDetails($clan);
$clan_members = ClanMembers::json_getMemberList($clan);
$join_requests = JoinRequests::json_getJoinRequests();
if ($join_requests == "") {
	$join_requests = '{}';
}
?>
<script>
var main = angular.module('main', []);

main.controller('ManageClan', function ($scope) {
	$scope.clans = <?=$clan_details?>
	$scope.members = <?=$clan_members?>
	$scope.parseInt = parseInt;
	$scope.requests = <?=$join_requests?>

	if (!$scope.requests.length) {
		$scope.numRequests = 0;
	} else {
		$scope.numRequests = $scope.requests.length;
	}

	$scope.numMembers = <?=Clans::numMembers()-1?>;

	$scope.noMembers = function () {
		if ($scope.numMembers == 0) {
			return true;
		} else {
			return false;
		}
	}

	$scope.add = function (index) {
		if ($scope.numMembers+1 >= 15) {
			alert("Sorry, but this action could not be completed because you have reached the maximum player limit.");
		} else {
			var username = $scope.requests[index]['username'];
			var proceed = confirm("Are you sure you want to accept "+username+" into the clan?");
			if (proceed == true) {
				$.ajax({
					url: "./add.php",
					dataType: "html",
					async: false,
					type: "POST",
					data: {username:username}
				});
				$scope.requests.splice(index, 1);
				$scope.numRequests = $scope.numRequests - 1;

				location.reload();
			}
		}
	}
	
	$scope.delete = function (index) {
		var username = $scope.requests[index]['username'];
		var proceed = confirm("Are you sure you want to delete "+username+"'s request to join?");
		if (proceed == true) {
			$.ajax({
				url: "./delete.php",
				dataType: "html",
				async: false,
				type: "POST",
				data: {username:username}
			});
			$scope.requests.splice(index, 1);
			$scope.numRequests = $scope.numRequests - 1;
		}
	}

	$scope.kick = function (index) {
		var username = $scope.members[index]['username'];
		var proceed = confirm("Are you sure you want to kick "+username+" from the clan?");
		if (proceed == true) {
			$.ajax({
				url: "./kick.php",
				dataType: "html",
				async: false,
				type: "POST",
				data: {username:username}
			});

			location.reload();
		}
	}
});
</script>