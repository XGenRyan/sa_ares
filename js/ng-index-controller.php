<?php
require_once(dirname(__DIR__)."/models.php");
$data = Clans::json_getClanList();
?>
<script>
var main = angular.module('main', ['ui.bootstrap']);

main.controller('ClanList', function($scope) {
	$scope.filteredclans = []
	,$scope.currentPage = 1
	,$scope.numPerPage = 10
	,$scope.maxSize = 5;

	$scope.makeclans = function() {
		$scope.clans = <?=$data?>
	};

	$scope.makeclans();

	$scope.numPages = function () {
		return Math.ceil($scope.clans.length / $scope.numPerPage);
	};

	$scope.$watch('currentPage + numPerPage', function() {
		var begin = (($scope.currentPage - 1) * $scope.numPerPage)
		, end = begin + $scope.numPerPage;

		$scope.filteredclans = $scope.clans.slice(begin, end);
	});
});
</script>