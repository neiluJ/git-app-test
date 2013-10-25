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
    };
    
    $scope.$on('changeCommit', function(event, currentCommit, reload) {
       $scope.currentCommit = currentCommit; 
       $scope.currentCommitHash = currentCommit.hash;
       $scope.currentCommitMessage = currentCommit.message;
       $scope.currentCommitDate = currentCommit.date;
       $scope.currentCommitAuthor = currentCommit.author;
       
       $scope.branch = currentCommit.hash;
       if (reload && $scope.repoAction == 'Repository') {
            $scope.browsePath(false, true);
        } else if(reload && $scope.repoAction == 'Blob') {
            $scope.browseBlob(false, true);
        }
    });
    
    $scope.$on('changePath', function(event, newPath) {
       computePathParts($scope);
    });
    
    $scope.repositoryBrowse = function($event) {
        $event.preventDefault();
        var url = $($event.target).attr('href');
        exploreFn(url, true);
    };
    
    $scope.browsePath = function(mergeCommits, fromCommits) {
        $('#blobContents').html("").hide();
        $scope.files = [];
        $http.get($scope.computeUrl()).success(function(data) {
            /* if (changeState == true) {
                history.pushState({path: data.path, repoName: $scope.repoName, branch: $scope.repoBranch}, null, url);
            } */
            $scope.files = data.files;
            $scope.path = data.path;
            
            if (!fromCommits) {
                $rootScope.$broadcast('changePath', $scope.path, mergeCommits);
            }
            
            $('#treeContents').show();
        }).error(function() {
            alert('Unable to load repository tree');
        });
    };
    
    $scope.browseBlob = function(mergeCommits, fromCommits) {
        $('#treeContents').hide();
        $scope.files = [];
        $http.get($scope.computeUrl().replace('Blob.action', 'BlobDisplay.action')).success(function(data) {
            $('#blobContents').html(data).show();
            if (!fromCommits) {
                $rootScope.$broadcast('changePath', $scope.path, mergeCommits);
            }
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
            $scope.browsePath(true);
        } else {
            $scope.repoAction = 'Blob';
            $scope.path = file.realpath;
            $scope.browseBlob(true);
        }
        $scope.$emit('viewChange');
    };
}]);

gitApp.controller('CommitsCtrl', ['$scope', '$http', '$rootScope', function CommitsCtrl($scope, $http, $rootScope) {
    $scope.commits      = [];
    $scope.currentCommit = {
        author: null,
        date: null,
        message: 'null',
        hash: 'null'
    };
    
    var _cache = [];
    
    var commitsCache = function(url) {
        var hash = MD5(url);
        if (_cache[hash] != undefined) {
            return _cache[hash];
        } 
        return false;
    };
    
    var loadCommits = function(url, emitEvent, merge, reload) {
        var cached = commitsCache(url);
        if (cached != false) {
            applyCommits(url, cached, emitEvent, merge, reload);
        } else {
            $http.get(url.replace($scope.repoAction +'.action', 'Commits.action')).success(function(data) {
                applyCommits(url, data, emitEvent, merge, reload);
            }).error(function() {
                alert('Cannot load commits :(');
            });
        }
    };
    
    var applyCommits = function(url, data, emitEvent, merge, reload) {
        $scope.currentCommit = data.jsonCurrentCommit;
            
        if (!merge) {
            $scope.commits       = data.jsonCommits;
        } else {
            angular.forEach(data.jsonCommits, function(commit, hash) {
                if ($scope.commits[commit.hash] == undefined) {
                    $scope.commits[commit.hash] = commit;
                }
            });

            angular.forEach($scope.commits, function(commit, hash) {
                if (data.jsonCommits[commit.hash] == undefined) {
                    $('.commit-'+ commit.hash.substring(0,6)).hide(500);
                } else {
                    $('.commit-'+ commit.hash.substring(0,6)).show(500);
                }
            });
        }

        if (emitEvent == true) {
            $rootScope.$broadcast('changeCommit', $scope.currentCommit, reload);
        }
        
        _cache[MD5(url)] = data;
    };
    
    $scope.browseRevisions = function($event, commit) {
        $event.preventDefault();
        if ($scope.currentCommit.hash == commit.hash) {
            return;
        }
        
        $scope.currentCommit = commit;
        
        $($event.target).parent().parent().find('li.active').removeClass('active');
        $($event.target).parent().addClass('active');
        
        $rootScope.$broadcast('changeCommit', commit, true);
        $('#repoBranch').val(commit.hash);
    };
    
    $scope.computeUrl = function(path) {
        return "./"+ $scope.repoAction 
            +'.action?name='+ $scope.repoName 
            +'&branch='+ $('#repoBranch').val() 
            + (path != null && path != '' ? '&path='+ path : '') 
            + '&ng=1';
    };
    
    $rootScope.$on('changePath', function($event, path, mergeCommits) {
        loadCommits($scope.computeUrl(path), true, mergeCommits, false);
    });
    
    loadCommits($scope.computeUrl(), true, false, true);
}]);