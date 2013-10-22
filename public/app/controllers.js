gitApp.controller('RepositoriesCtrl', function RepositoriesCtrl($scope, $http) {
    $http.get('Repositories.action?angular').success(function(data) {
        $scope.repositories = data.repositories;
    }).error(function() {
        alert('Unable to load repositories');
    });
});
    
var computePathParts = function($scope) {
    if ($scope.path != null && $scope.path != undefined) {
        var parts = [], split = $scope.path.split('/'), idx, link = [];
        for (idx = 0; idx < split.length; ++idx) {
            link.push(split[idx]);
            parts.push({path: split[idx], link: link.join('/')});
        }

        $scope.pathParts = parts;
    } else {
        $scope.pathParts = [];
    }
};
    
gitApp.controller('RepositoryCtrl', function RepositoryCtrl($scope, $http) {
        
    $scope.repoName = $('#repoName').html();
    $scope.branch   = $('#repoBranch').html();
    $scope.files    = [];
    $scope.branches = [];
    $scope.path     = $('#repoPath').val();
    
    $scope.pathParts = [];
    
    var exploreFn = function(url, changeState) {
        $scope.files = [];
        $http.get(url.replace('Repository.action', 'Tree.action')).success(function(data) {
            if (changeState == true) {
                history.pushState({path: data.path, repoName: $scope.repoName, branch: $scope.repoBranch}, null, url);
            }
            $scope.files = data.files;
            $scope.path = data.path;
            computePathParts($scope);
        }).error(function() {
            alert('Unable to load repository tree');
        });
    };
    
    computePathParts($scope);
    
    exploreFn(window.location.href, true);
    
    $scope.repositoryBrowse = function($event) {
        $event.preventDefault();
        var url = $($event.target).attr('href');
        exploreFn(url, true);
    };
    
    window.onpopstate = function(e) {
        exploreFn(window.location.href, false);
    }
});

gitApp.controller('RepositoryBlob', function RepositoryBlob($scope, $http) {
        
    $scope.repoName = $('#repoName').html();
    $scope.branch   = $('#repoBranch').html();
    $scope.commits  = [];
    $scope.currentCommit = {
        author: null,
        date: null,
        message: null,
        hash: null
    };
    $scope.path         = $('#repoPath').val();
    $scope.pathParts    = [];
    $scope.blobContents = "";
    
    computePathParts($scope);
    
    var exploreFn = function(url, changeState, replaceCommits) {
        $http.get(url.replace('Blob.action', 'BlobInfos.action')).success(function(data) {
            if (changeState == true) {
                history.pushState({path: data.path, repoName: $scope.repoName, branch: $scope.repoBranch}, null, url);
            }
            $scope.currentCommit = data.currentCommit;
            if (replaceCommits == true) {
                $scope.commits = data.commits;
            }
            showBlob(url);
        }).error(function() {
            alert('Unable to load blob revision');
        });
    };
    
    var showBlob = function(url) {
        $http.get(url.replace('Blob.action', 'BlobDisplay.action')).success(function(data) {
            $scope.blobContents = data;
            $('#blobContents').html(data);
        }).error(function() {
            alert('Unable to load blob contents');
        });
    };
    
    exploreFn(window.location.href, false, true);
    
    $scope.blobBrowseRevisions = function($event) {
        $event.preventDefault();
        if ($($event.target).parent().parent().hasClass('active')) {
            return;
        }
        
        var url = $($event.target).attr('href');
        $($event.target).parent().parent().find('li.active').removeClass('acitve');
        $($event.target).parent().addClass('active');
        exploreFn(url, true);
    };
    
    window.onpopstate = function(e) {
        exploreFn(window.location.href, false);
    }
});
