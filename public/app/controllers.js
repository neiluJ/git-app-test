var gitApp = angular.module('gitAppControllers', []); 

gitApp.controller('RepositoriesCtrl', ['$scope', '$http', function RepositoriesCtrl($scope, $http) {
    $http.get('Repositories.action?angular').success(function(data) {
        $scope.repositories = data.repositories;
    }).error(function() {
        alert('Unable to load repositories');
    });
}]);
    
gitApp.controller('RepositoryMainCtrl', ['$scope', '$http', function RepositoryMainCtrl($scope, $http) {
    $scope.repoName     = $('#repoName').html();
    $scope.branch       = $('#repoBranch').val();
    $scope.files        = [];
    $scope.branches     = [];
    $scope.path         = $('#repoPath').val();
    $scope.repoAction   = $('#repoAction').val();
}]);

gitApp.controller('RepositoryDisplayCtrl', ['$scope', '$rootScope', function RepositoryDisplayCtrl($scope, $rootScope) {
    $scope.pathParts = [];
    
    $scope.currentCommitHash = null;
    $scope.currentCommitMessage = null;
    $scope.currentCommitDate = null;
    $scope.currentCommitAuthor = null;
    
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

    computePathParts($scope);
    
    $scope.$on('changeCommit', function(event, currentCommit) {
       $scope.currentCommit = currentCommit; 
       $scope.currentCommitHash = currentCommit.hash;
       $scope.currentCommitMessage = currentCommit.message;
       $scope.currentCommitDate = currentCommit.date;
       $scope.currentCommitAuthor = currentCommit.author;
    });
    
    $scope.$on('changePath', function(event, newPath) {
       $scope.path = newPath; 
       computePathParts($scope);
    });
}]);

gitApp.controller('CommitsCtrl', ['$scope', '$http', '$rootScope', function CommitsCtrl($scope, $http, $rootScope) {
    $scope.commits      = [];
    $scope.currentCommit = {
        author: null,
        date: null,
        message: 'null',
        hash: 'null'
    };
    
    var loadCommits = function(url, emitEvent) {
        $http.get(url.replace($scope.repoAction +'.action', 'Commits.action')).success(function(data) {
            $scope.currentCommit = data.jsonCurrentCommit;
            $scope.commits       = data.jsonCommits;
            if (emitEvent == true) {
                $rootScope.$broadcast('changeCommit', $scope.currentCommit);
            }
        }).error(function() {
            alert('Cannot load commits :(');
        });
    };
    
    $scope.browseRevisions = function($event) {
        $event.preventDefault();
        if ($($event.target).parent().parent().hasClass('active')) {
            return;
        }
        
        var hashTmp = $($event.target).html();
        $($scope.commits).each(function(i, item) {
             if (item.hash.substring(0,6) == hashTmp) {
                 $scope.currentCommit = item;
             }
        });
        
        $($event.target).parent().parent().find('li.active').removeClass('acitve');
        $($event.target).parent().addClass('active');
        
        $rootScope.$broadcast('changeCommit', $scope.currentCommit);
    };
    
    loadCommits(window.location.href, true);
}]);

gitApp.controller('BlobCtrl', ['$scope', '$http', '$rootScope', function BlobCtrl($scope, $http, $rootScope) {
        
    $scope.blobContents = "";
    
    var showBlob = function(url) {
        $http.get(url.replace('Blob.action', 'BlobDisplay.action')).success(function(data) {
            $scope.blobContents = data;
            $('#blobContents').html(data);
            $rootScope.$broadcast('changePath', $scope.path);
        }).error(function() {
            alert('Unable to load blob contents');
        });
    };
    
    showBlob(window.location.href);
}]);

gitApp.controller('TreeCtrl', ['$scope', '$http', '$rootScope', function TreeCtrl($scope, $http, $rootScope) {
        
    $scope.files = [];
    
    var exploreFn = function(url) {
        $scope.files = [];
        $http.get(url.replace('Repository.action', 'Tree.action')).success(function(data) {
            /* if (changeState == true) {
                history.pushState({path: data.path, repoName: $scope.repoName, branch: $scope.repoBranch}, null, url);
            } */
            $scope.files = data.files;
            $scope.path = data.path;
            
            $rootScope.$broadcast('changePath', $scope.path);
        }).error(function() {
            alert('Unable to load repository tree');
        });
    };
    
    $scope.repositoryBrowse = function($event) {
        $event.preventDefault();
        var url = $($event.target).attr('href');
        exploreFn(url, true);
    };
    
    exploreFn(window.location.href); 
}]);

