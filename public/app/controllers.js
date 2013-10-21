gitApp.controller('RepositoriesCtrl', function RepositoriesCtrl($scope, $http) {
        
    $http.get('Repositories.action?angular').success(function(data) {
        $scope.repositories = data.repositories;
    }).error(function() {
        alert('Unable to load repositories');
    });
});
    
gitApp.controller('RepositoryCtrl', function RepositoryCtrl($scope, $http) {
        
    $scope.repoName = $('#repoName').html();
    $scope.branch   = $('#repoBranch').html();
    $scope.files    = [];
    $scope.commits  = [];
    $scope.branches = [];
    $scope.path     = $('#repoPath').val();
    
    $scope.pathParts = ($scope.path != null ? $scope.path.split('/') : []);
    
    $http.get('Tree.action?name='+ $scope.repoName +'&branch='+ $scope.branch + ($scope.path != null ? '&path='+ $scope.path : '') +'&angular').success(function(data) {
        $scope.files = data.files;
    }).error(function() {
        alert('Unable to load repository tree');
    });
});
    
