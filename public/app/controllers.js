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
gitApp.controller('RepositoryDisplayCtrl', ['$scope', '$rootScope', '$http', '$compile', 'RepoNavService', function RepositoryDisplayCtrl($scope, $rootScope, $http, $compile, RepoNavService) {
    $scope.files        = [];
    $scope.pathParts    = [];
    $scope.blob         = "";
    $scope.commitInfos  = "";
    $scope.commitInfosHash   = "";
    $scope.compareCommit     = "";
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
    
    $scope.$on('compare', function(event, commit1, commit2, browsing) {
       RepoNavService.showCompare($scope, commit2.hash.substring(0,6) + '..' + commit1.hash.substring(0,6), browsing);
    });
    
    $scope.$watch('files', function() {
       if (!$scope.files || $scope.files.length == 0) {
           $('#treeContents').hide();
           return;
       }
       $scope.blob          = null;
       $scope.commitInfos   = null;
       $scope.compareCommit = null;
       $('.repo-path').show(400);
       $('#repo-commit').show(200);
       $('#treeContents').show();
    });
    
    $scope.$watch('blob', function() {
        if ($scope.blob == null) {
            $('#blobContents').html("").hide();
            return;
        }
        $scope.files         = [];
        $scope.commitInfos   = null;
        $scope.compareCommit = null;
        $('.repo-path').show(400);
        $('#repo-commit').show(200);
        $('#blobContents').html($scope.blob).show();
    });
    
    $scope.$watch('commitInfos', function() {
        if ($scope.commitInfos == null) {
            $('#commitContents').html("");
            return;
        }
        $scope.blob             = null;
        $scope.files            = [];
        
        $('.repo-path').hide(400);
        $('#repo-commit').show(200);
        $('#commitContents').show().html(
            $compile($('#commitContents').html())($scope)
        );
        $('.commits-list').find('li.active').removeClass('active');
        $('.commits-list').find('a.commit-'+ $scope.commitInfosHash.substring(0,6)).parent().parent().addClass('active');
    });
    
    $scope.$watch('compareCommit', function() {
        if ($scope.compareCommit == null) {
            $('#commitContents').html("");
            return;
        }
        $scope.blob             = null;
        $scope.files            = [];
        $('.repo-path').hide(400);
        $('#repo-commit').hide(200);
        $('#commitContents').show().html(
            $compile($('#commitContents').html())($scope)
        );
    });
    
    $scope.navigateToFile = function($event, file) {
        if ($event) {
            $event.preventDefault();
        }
        
        return RepoNavService.changePath($scope, file.realpath, !file.directory, ($event != undefined ? true : false), true);
    };
    
    $scope.navigateToBlob = function($event, realpath, hash) {
        if ($event) {
            $event.preventDefault();
        }
        if (hash !== undefined && hash != null) {
            $scope.branch = hash;
        }
        
        return RepoNavService.changePath($scope, realpath, true, ($event != undefined ? true : false), true);
    };
    
    $scope.navigateToCommit = function($event, commit) {
        if ($event) {
            $event.preventDefault();
        }
        
        return RepoNavService.showCommit($scope, commit, true);
    };
    
    $scope.navigateToRoot = function($event, hash) {
        if ($event) {
            $event.preventDefault();
        }
        
        $scope.branch = hash;
        return RepoNavService.changePath($scope, null, false, ($event != undefined ? true : false), true);
    };
    
    $scope.$on('init', function() {
        if ($scope.repoAction == 'Repository' || $scope.repoAction == 'Blob') {
            RepoNavService.changePath($scope, $scope.path, ($scope.repoAction == 'Blob'), true, false);
            computePathParts($scope.path);
        } else if ($scope.repoAction == 'Commit') {
            RepoNavService.showCommit($scope, $('#commitHash').val(), true);
        } else if ($scope.repoAction == 'Compare') {
            RepoNavService.showCompare($scope, $('#repoCompare').val(), true);
        }
    });
    
    $scope.$on('changeCommit', function($event, commit) { 
        $scope.branch = commit.hash;
        if ($scope.repoAction == 'Repository' || $scope.repoAction == 'Blob') {
            RepoNavService.changePath($scope, $scope.path, ($scope.repoAction == 'Blob'), true, false);
        } else {
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
            
            $rootScope.$broadcast('compare', $scope.currentCommit, commit, true);
        }
    };
    
    $rootScope.$on('changePath', function(event, newPath, pathChanged) {
        if (pathChanged) {
            RepoNavService.loadCommits($scope, true);
        }
    });
    
    $scope.$on('changedCommit', function($event, commit) {
       $rootScope.$broadcast('changeCommit', commit);
    });
    
    setTimeout(function() {
        RepoNavService.loadCommits($scope, true, ($scope.repoAction == 'Commit' ? $('#commitHash').val() : false));
        $rootScope.$broadcast('init')
    }, 1);
}]);