gitApp.controller('RepositoriesCtrl', function RepositoriesCtrl($scope, $http) {
        
    $http.get('./index.php/Repositories.action').success(function(data) {
        $scope.repositories = data;
    }).error(function() {
        alert('Unable to load repositories');
    });
});
    
