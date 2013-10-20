gitApp.controller('RepositoriesCtrl', function RepositoriesCtrl($scope, $http) {
        
    $http.get('Repositories.action').success(function(data) {
        $scope.repositories = data;
    }).error(function() {
        alert('Unable to load repositories');
    });
});
    
