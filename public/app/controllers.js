gitApp.controller('RepositoriesCtrl', function RepositoriesCtrl($scope, $http) {
        
    $http.get('Repositories.action?angular').success(function(data) {
        $scope.repositories = data.repositories;
    }).error(function() {
        alert('Unable to load repositories');
    });
});
    
gitApp.controller('RepositoryCtrl', function RepositoryCtrl($scope, $http) {
        
    $scope.repoName = $('#repoName').html();
    $scope.branch = $('#repoBranch').html();
    
    $http.get('Tree.action?name='+ $scope.repoName +'&branch='+ $scope.branch +'&angular').success(function(data) {
        $scope.files = data.files;
    }).error(function() {
        alert('Unable to load repository tree');
    });
});
    
