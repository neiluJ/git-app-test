var gitApp = angular.module('gitAppControllers', ['gitAppServices']); 

// ------------------------
// Controller: Repositories
// ------------------------
// 
// This controller is used to fetch the repositories list
//
gitApp.controller('RepositoriesCtrl', ['$scope', '$http', function RepositoriesCtrl($scope, $http) {
    $http.get('Repositories.action?angular').success(function(data) {
        $scope.repositories = data.repositories;
    }).error(function() {
        alert('Unable to load repositories');
    });
}]);

// --------------------------
// Controller: RepositoryMain
// --------------------------
// 
// This controller is the main controller for repository display
//
gitApp.controller('RepositoryMainCtrl', ['$scope', '$http', 'RepoNavService', function RepositoryMainCtrl($scope, $http, RepoNavService) {
    $scope.repoName     = $('#repoName').html();
    $scope.branch       = $('#repoBranch').val();
    $scope.files        = [];
    $scope.branches     = [];
    $scope.path         = $('#repoPath').val();
    $scope.repoAction   = $('#repoAction').val();
}]);

// -----------------------------
// Controller: RepositoryDisplay
// -----------------------------
// 
// This controller is responsible of the global repository navigation.
//
gitApp.controller('RepositoryDisplayCtrl', ['$scope', '$rootScope', '$http', 'RepoNavService', function RepositoryDisplayCtrl($scope, $rootScope, $http, RepoNavService) {
    $scope.files        = [];
    $scope.pathParts    = [];
    $scope.blob         = "";
    $scope.commitInfos  = "";
    $scope.commitInfosHash = "";
    
    $scope.currentCommitHash = null;
    $scope.currentCommitMessage = null;
    $scope.currentCommitDate = null;
    $scope.currentCommitAuthor = null;
    
    var computePathParts = function(path) {
        var parts = [{path: $scope.repoName, realpath: '', directory: true}];
        if (!path) {
            path = "";
        }
        
        if (path != null && path != undefined) {
            var split = path.split('/'), idx, link = [];
            for (idx = 0; idx < split.length; idx++) {
                if (split[idx] == "") { continue; }
                link.push(split[idx]);
                var isDir = (idx >= split.length-1 ? ($scope.repoAction == 'Blob' ? false : true) : true);
                parts.push({path: split[idx], realpath: link.join('/'), directory: isDir});
            }
        }
        
        $scope.pathParts = parts;
    };

    computePathParts();
    
    $scope.$on('currentCommitChange', function($event, currentCommit) {
       $scope.currentCommitHash = currentCommit.hash;
       $scope.currentCommitMessage = currentCommit.message;
       $scope.currentCommitDate = currentCommit.date;
       $scope.currentCommitAuthor = currentCommit.author;
    });
    
    $scope.$on('changePath', function(event, newPath) {
       computePathParts(newPath);
    });
    
    $scope.$on('compare', function(event, url) {
       $scope.browseCompare(url);
    });
    
    $scope.$watch('files', function() {
       $('.repo-path').show(400);
       $('#repo-commit').show(400);
       if (!$scope.files || $scope.files.length == 0) {
           $('#treeContents').hide();
           return;
       }
       $('#commitContents').html("").hide();
       $('#treeContents').show();
    });
    
    $scope.$watch('blob', function() {
        $('.repo-path').show(400);
        $('#repo-commit').show(400);
        if ($scope.blob == null) {
            $('#blobContents').html("").hide();
            return;
        }
        $('#commitContents').html("").hide();
        $('#blobContents').html($scope.blob).show();
    });
    
    $scope.$watch('commitInfos', function() {
        if ($scope.commitInfos == null) {
            $('#commitContents').html("").hide();
            return;
        }
        $('#treeContents').hide();
        $('#blobContents').html("").hide();
        $('#repo-commit').show(400);
        $('.repo-path').hide(400);
        $('#commitContents').html($scope.commitInfos).show();
        $('.commits-list').find('li.active').removeClass('active');
        $('.commits-list').find('a.commit-'+ $scope.commitInfosHash.substring(0,6)).parent().parent().addClass('active');
    });
    
    $scope.browseCompare = function(url) {
        $('#treeContents').hide();
        $('#blobContents').html("").hide();
        $('#repo-commit').hide(400);
        $('.repo-path').hide(400);
        $http.get(url).success(function(data) {
            $('#commitContents').html(data).show();
        }).error(function() {
            alert('Unable to load comparision');
        });
    };
    
    $scope.navigateToFile = function($event, file) {
        if ($event) {
            $event.preventDefault();
        }
        
        return RepoNavService.changePath($scope, file.realpath, !file.directory, ($event != undefined ? true : false), true);
    };
    
    $scope.navigateToCommit = function($event, commit) {
        if ($event) {
            $event.preventDefault();
        }
        
        return RepoNavService.showCommit($scope, commit);
    };
    
    $scope.$on('init', function() {
        if ($scope.repoAction == 'Repository' || $scope.repoAction == 'Blob') {
            RepoNavService.changePath($scope, $scope.path, ($scope.repoAction == 'Blob'), true, false);
            computePathParts($scope.path);
        } else if ($scope.repoAction == 'Commit') {
            RepoNavService.showCommit($scope, $('#commitHash').val(), true);
        }
    });
    
    $scope.$on('changeCommit', function($event, commit) { 
        if ($scope.repoAction == 'Repository' || $scope.repoAction == 'Blob') {
            RepoNavService.changePath($scope, $scope.path, ($scope.repoAction == 'Blob'), true, false);
        } else if ($scope.repoAction == 'Commit') {
            RepoNavService.showCommit($scope, commit.hash, true);
        }
    });
    
    RepoNavService.init($scope.repoName, $scope.repoAction, $scope.path, $scope.branch, $scope);
}]);

// -----------------------
// Controller: CommitsCtrl
// -----------------------
// 
// This controller is used to fetch commits from the current browsing path.
//
gitApp.controller('CommitsCtrl', ['$scope', '$http', '$rootScope', 'RepoNavService', function CommitsCtrl($scope, $http, $rootScope, RepoNavService) {
    $scope.commits      = [];
    $scope.currentCommit = {
        author: null,
        date: null,
        message: null,
        hash: null
    };
    $scope.currentCommitHash = null;
    $scope.currentCommitMessage = null;
    $scope.currentCommitDate = null;
    $scope.currentCommitAuthor = null;
    
    // notify other controllers about commit change
    $scope.$watch('currentCommit', function() {
       if (!$scope.currentCommit || $scope.currentCommit.hash == "null" || !$scope.currentCommit.hash) { return; }
       $rootScope.$broadcast('currentCommitChange', $scope.currentCommit);
    });
    
    var initComparision = function(commit1, commit2) {
        var path = $('#repoPath').val(),
            url = "./Compare"
            +'.action?name='+ $scope.repoName
            +'&compare='+ commit2.hash.substring(0,6) +'..'+ commit1.hash.substring(0,6) 
            + ($scope.path != null && $scope.path != '' ? '&path='+ $scope.path : '') 
            + '&ng=1';
        
        $rootScope.$broadcast('compare', url);
    };
    
    $scope.browseRevisions = function($event, commit) {
        $event.preventDefault();
        if ($scope.currentCommit.hash == commit.hash) {
            return;
        }
        
        if (!$event.ctrlKey) {
            $($event.target).parent().parent().find('li.active').removeClass('active');
            $($event.target).parent().addClass('active');
            $($event.target).parent().parent().parent().find('li.compare').removeClass('compare');
            
            RepoNavService.defineCurrentCommit($scope, commit, true);
        } else {
            $($event.target).parent().parent().parent().find('li.compare').removeClass('compare');
            $($event.target).parent().parent().addClass('compare');
            
            return RepoNavService.showCompare($scope.currentCommit, commit, true);
            initComparision($scope.currentCommit, commit, $scope.path);
        }
    };
    
    setTimeout(function() {
        RepoNavService.loadCommits($scope, true, ($scope.repoAction == 'Commit' ? $('#commitHash').val() : false));
        $rootScope.$broadcast('init')
    }, 1);
    
    $rootScope.$on('changePath', function(event, newPath) {
       RepoNavService.loadCommits($scope, true);
    });
    
    $scope.$on('changedCommit', function($event, commit) {
       $rootScope.$broadcast('changeCommit', commit);
    });
}]);