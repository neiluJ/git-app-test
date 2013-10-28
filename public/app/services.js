var gitAppServices = angular.module('gitAppServices', []);
     
var RepoNavService = function($http) {
    this.$http          = $http;
    this.repoName       = null;
    this.repoAction     = null;
    this.repoPath       = null;
    this.repoBranch     = null;
    this.currentCommit  = null;
    this.$scope         = {};
    this.commitsCache   = [];
};

RepoNavService.prototype.init = function(name, action, path, branch) {
    this.repoName       = name;
    this.repoAction     = action;
    this.repoPath       = path;
    this.repoBranch     = branch;
    this.currentCommit  = undefined;
    
    // load the view
    console.log('initializing : '+ this.repoAction +': '+ (this.repoPath || '/') +' ('+ this.repoBranch +')');
};

RepoNavService.prototype.defineCurrentCommit = function($scope, commit, browsing) {
    this.currentCommit      = commit;
    $scope.currentCommit    = commit;
    
    if (browsing) {
        $scope.$broadcast('changedCommit', commit);
    }
    
    console.log('changed commit : '+ this.repoAction +' -> '+ this.repoPath +' ('+ this.repoBranch +'): '+ commit.hash);
};

RepoNavService.prototype.changePath = function($scope, newPath, isBlob, browsing, notifyPath) {
     
    this.repoPath       = newPath;
    this.repoAction     = (isBlob ? 'Blob' : 'Repository');
    $scope.repoAction   = this.repoAction;
    
    var url = "./"+ this.repoAction 
            +'.action?name='+ this.repoName 
            +'&branch='+ (this.currentCommit == undefined ? this.repoBranch : this.currentCommit.hash)
            + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '') 
            + '&ng=1',  self = this;
    
    var afterHttpGet = function($scope, newPath, browsing, notifyPath) {
        if (notifyPath == true) {
            $scope.$emit('changePath', newPath, true);
        }
        self.navigate(browsing);
    };
    
    if (this.repoAction == 'Repository') {
        this.$http.get(url).success(function(data) {
            $scope.blob     = "";
            $scope.files    = data.files;
            $scope.path     = newPath;
            afterHttpGet($scope, newPath, browsing, notifyPath);
        }).error(function() {
            alert('Unable to load repository tree');
        });
    } else {
        this.$http.get(url).success(function(data) {
            $scope.files    = [];
            $scope.blob     = data;
            $scope.path     = newPath;
            afterHttpGet($scope, newPath, browsing, notifyPath);
        }).error(function() {
            alert('Unable to load blob');
        });
    }
};

RepoNavService.prototype.showCommit = function($scope, commit, browsing) {
    var url = './Commit.action?name='+ this.repoName +'&hash='+ commit, self = this;
    this.repoAction     = 'Commit';
    $scope.repoAction   = 'Commit';
    
    this.$http.get(url).success(function(data) {
        $scope.commitInfosHash = commit;
        $scope.commitInfos = data;
        self.navigate(browsing);
    }).error(function() {
        alert('Unable to load commit');
    });
};

RepoNavService.prototype.showCompare = function(commit, commit2, browsing) {
    
    this.navigate(browsing);
    
    console.log('showing compare between : '+ commit.hash + ' and '+ commit2.hash);
};

RepoNavService.prototype._loadCommitsFromCache = function(url) {
    var hash = MD5(url);
    if (this.commitsCache[hash] != undefined) {
        return this.commitsCache[hash];
    }
    return false;
};
    
RepoNavService.prototype.loadCommits = function($scope, merge, current) {
    var url = "./Commits"
            +'.action?name='+ this.repoName 
            +'&branch='+ (this.currentCommit == undefined ? this.repoBranch : this.currentCommit.hash)
            + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '') 
            + '&ng=1', self = this;
    
    var applyCommits = function($scope, url, data, merge, current) {
        if (!current) {
            $scope.currentCommit = data.jsonCurrentCommit;
        } else {
            $scope.currentCommit = data.jsonCommits[current];
        }
        
        if (!merge) {
            $scope.commits       = data.jsonCommits;
        } else {
            for(var key in data.jsonCommits) {
                if ($scope.commits[key] == undefined) {
                    $scope.commits[key] = data.jsonCommits[key];
                }
            }
            
            for(var key in $scope.commits) {
                if (data.jsonCommits[key] == undefined) {
                    $('.commit-'+ key.substring(0,6)).hide(500);
                } else {
                    $('.commit-'+ key.substring(0,6)).show(500);
                }
            }
        }

        self.commitsCache[MD5(url)] = data;
    };
    
    var cached = this._loadCommitsFromCache(url);    
    if (cached != false) {
        applyCommits($scope, url, cached, merge, current);
    } else {
        this.$http.get(url).success(function(data) {
            applyCommits($scope, url, data, merge, current);
        }).error(function() {
            alert('Cannot load commits :(');
        });
    }
};

RepoNavService.prototype.navigate = function(pushState) {
    
    var url = "./"+ this.repoAction 
            +'.action?name='+ this.repoName 
            +'&branch='+ (this.currentCommit == undefined ? this.repoBranch : this.currentCommit.hash)
            + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '') 
            + '&ng=1';
        
    // console.log(url);    
};

gitAppServices.factory('RepoNavService', ['$http', function($http) {
    return new RepoNavService($http);
}]);

/*
 * window.onpopstate = function(event) {
        var state = event.state;
        if (state.action == 'Repository') {
            $scope.repoAction = 'Repository';
            $scope.path = state.path;
            $scope.browsePath(true, false, false);
        } else if (state.action == 'Blob') {
            $scope.repoAction = 'Blob';
            $scope.path = state.path;
            $scope.browseBlob(true, false, false);
        } else if (state.action == 'Commit') {
            $scope.navigateToCommit(event, state.hash, false);
        }
        // alert("location: " + document.location + ", state: " + JSON.stringify(event.state));
    };
 */