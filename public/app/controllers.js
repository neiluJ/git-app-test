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

gitApp.controller('RepositoryDisplayCtrl', ['$scope', '$rootScope', '$http', function RepositoryDisplayCtrl($scope, $rootScope, $http) {
    $scope.files        = [];
    $scope.pathParts    = [];
    
    $scope.currentCommitHash = null;
    $scope.currentCommitMessage = null;
    $scope.currentCommitDate = null;
    $scope.currentCommitAuthor = null;
    
    var computePathParts = function() {
        var parts = [{path: $scope.repoName, realpath: '', directory: true}];
        if ($scope.path != null && $scope.path != undefined) {
            var split = $scope.path.split('/'), idx, link = [];
            for (idx = 0; idx < split.length; idx++) {
                link.push(split[idx]);
                var isDir = (idx >= split.length-1 ? ($scope.repoAction == 'Blob' ? false : true) : true);
                parts.push({path: split[idx], realpath: link.join('/'), directory: isDir});
            }
        }
        
        $scope.pathParts = parts;
    };

    computePathParts($scope);
    
    $scope.computeUrl = function() {
        return "./"+ $scope.repoAction 
            +'.action?name='+ $scope.repoName 
            +'&branch='+ $scope.branch 
            + ($scope.path != null && $scope.path != '' ? '&path='+ $scope.path : '') 
            + '&ng=1';
    }
    
    $scope.$on('changeCommit', function(event, currentCommit) {
       $scope.currentCommit = currentCommit; 
       $scope.currentCommitHash = currentCommit.hash;
       $scope.currentCommitMessage = currentCommit.message;
       $scope.currentCommitDate = currentCommit.date;
       $scope.currentCommitAuthor = currentCommit.author;
       
       console.log('commit changed: '+ currentCommit.hash);
    });
    
    $scope.$on('changePath', function(event, newPath) {
       computePathParts($scope);
    });
    
    $scope.repositoryBrowse = function($event) {
        $event.preventDefault();
        var url = $($event.target).attr('href');
        exploreFn(url, true);
    };
    
    $scope.browsePath = function() {
        $('#blobContents').html("").hide();
        $scope.files = [];
        $http.get($scope.computeUrl()).success(function(data) {
            /* if (changeState == true) {
                history.pushState({path: data.path, repoName: $scope.repoName, branch: $scope.repoBranch}, null, url);
            } */
            $scope.files = data.files;
            $scope.path = data.path;
            $rootScope.$broadcast('changePath', $scope.path);
            
            $('#treeContents').show();
        }).error(function() {
            alert('Unable to load repository tree');
        });
    };
    
    $scope.browseBlob = function() {
        $('#treeContents').hide();
        $scope.files = [];
        $http.get($scope.computeUrl().replace('Blob.action', 'BlobDisplay.action')).success(function(data) {
            $('#blobContents').html(data).show();
            $rootScope.$broadcast('changePath', $scope.path);
        }).error(function() {
            alert('Unable to load blob');
        });
    };
    
    $scope.navigateToFile = function($event, file) {
        if ($event) {
            $event.preventDefault();
        }
        
        if (file.directory) {
            $scope.repoAction = 'Repository';
            $scope.path = file.realpath;
            $scope.browsePath();
        } else {
            $scope.repoAction = 'Blob';
            $scope.path = file.realpath;
            $scope.browseBlob();
        }
    };
    
    if ($scope.repoAction == 'Repository') {
        $scope.browsePath();
    } else {
        $scope.browseBlob();
    }
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