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

RepoNavService.prototype.init = function(name, action, path, branch, $scope) {
    this.repoName       = name;
    this.repoAction     = action;
    this.repoPath       = path;
    this.oldRepoPath    = "";
    this.repoBranch     = branch;
    this.currentCommit  = undefined;
    this.repoCompare    = undefined;
    
    window.__repoNav    = this;
    this.lastScope      = $scope;
};

RepoNavService.prototype.defineCurrentCommit = function($scope, commit, browsing) {
    this.currentCommit      = commit;
    $scope.currentCommit    = commit;
    
    if (browsing) {
        $scope.$broadcast('changedCommit', commit);
    }
};

RepoNavService.prototype.changePath = function($scope, newPath, isBlob, browsing, notifyPath) {
     
    this.oldRepoPath    = this.repoPath;
    this.repoAction     = (isBlob ? 'Blob' : 'Repository');
    $scope.repoAction   = this.repoAction;
    this.repoPath = $scope.path = newPath;
    
    var url = "./"+ this.repoAction 
            +'.action?name='+ this.repoName 
            +'&branch='+ $scope.branch 
            + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '') 
            + '&ng=1',  self = this;
    
    var afterHttpGet = function($scope, newPath, browsing, notifyPath) {
        if (notifyPath == true) {
            $scope.$emit('changePath', newPath, true, (self.oldRepoPath == newPath));
        }
        self.navigate($scope, browsing);
    };
    
    if (this.repoAction == 'Repository') {
        this.$http.get(url).success(function(data) {
            $scope.blob         = "";
            $scope.files        = data.files;
            afterHttpGet($scope, newPath, browsing, notifyPath);
        }).error(function() {
            alert('Unable to load repository tree');
        });
    } else {
        this.$http.get(url).success(function(data) {
            $scope.files    = [];
            $scope.blob     = data;
            afterHttpGet($scope, newPath, browsing, notifyPath);
        }).error(function() {
            alert('Unable to load blob');
        });
    }
};

RepoNavService.prototype.showCommit = function($scope, commit, browsing) {
    var url = './Commit.action?name='+ this.repoName +'&hash='+ commit, 
        self = this;
    
    this.$http.get(url).success(function(data) {
        self.currentCommit      = commit;
        $scope.commitInfosHash  = commit;
        $scope.commitInfos      = Math.random();
        self.repoAction = $scope.repoAction = 'Commit';
        self.repoBranch         = commit;
        $('#commitContents').html(data);
        self.navigate($scope, browsing);
    }).error(function() {
        alert('Unable to load commit');
    });
};

RepoNavService.prototype.showCompare = function($scope, comparision, browsing) {
    var url = "./Compare"
            +'.action?name='+ this.repoName 
            +'&compare='+ comparision
            +'&path='+ this.repoPath
            +'&ng=1', self = this;
    
    this.$http.get(url).success(function(data) {
        self.repoAction = $scope.repoAction = 'Compare';
        self.repoCompare = comparision;
        $scope.compareCommit = Math.random();
        $('#commitContents').html(data);
        self.navigate($scope, browsing);
    }).error(function() {
        alert('Unable to load comparision');
    });
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
            +'&branch='+ (this.currentCommit == undefined ? this.repoBranch : (angular.isObject(this.currentCommit) ? this.currentCommit.hash : this.currentCommit))
            + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '') 
            + '&ng=1', self = this;
    
    var applyCommits = function($scope, url, data, merge, current) {
        if (!current) {
            $scope.currentCommit = data.jsonCurrentCommit;
            self.currentCommit = data.jsonCurrentCommit;
        } else {
            $scope.currentCommit = data.jsonCommits[current];
            self.currentCommit  = data.jsonCommits[current];
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
                    $('.commit-'+ key.substring(0,6)).hide(250);
                } else {
                    $('.commit-'+ key.substring(0,6)).show(250);
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

RepoNavService.prototype.navigate = function($scope, pushState) {
    
    if (!pushState) {return;}
    
    var url = "./"+ this.repoAction +'.action?name='+ this.repoName, 
        self = this,
        data = {
        action: this.repoAction,
        path: this.repoPath,
        branch: this.repoBranch,
        currentCommit: (this.currentCommit.hash != undefined ? this.currentCommit.hash : this.currentCommit),
        comparision: this.repoCompare
    };
            
    if (this.repoAction == 'Repository' || this.repoAction == 'Blob') {
        url += '&branch='+ (this.currentCommit.hash ? this.currentCommit.hash : this.currentCommit)
         + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '');
    } else if (this.repoAction == 'Commit') {
        url += '&hash='+ (this.currentCommit == undefined ? this.repoBranch : (angular.isObject(this.currentCommit) ? this.currentCommit.hash : this.currentCommit));    
    } else if (this.repoAction == 'Compare') {
        url += '&compare='+ this.repoCompare
            + (this.repoPath != null && this.repoPath != '' ? '&path='+ this.repoPath : '');
    }
    
    window.history.pushState(data, null, url);
};

RepoNavService.prototype.reverseNavigate = function(state) {
    if (!state || !this.lastScope) {return;}
    
    if (state.action == 'Repository' || state.action == 'Blob') {
        this.currentCommit  = state.currentCommit;
        this.branch         = state.branch;
        this.changePath(this.lastScope, state.path, (state.action == 'Blob'), false, true);
    } else if (state.action == 'Commit') {
        this.showCommit(this.lastScope, state.currentCommit, false);
    } else if (state.action == 'Compare') {
        this.showCompare(this.lastScope, state.comparision, false);
    }
};


gitAppServices.factory('RepoNavService', ['$http', function($http) {
    return new RepoNavService($http);
}]);

window.onpopstate = function(event) {
    if (!window.__repoNav) {return;}
    window.__repoNav.reverseNavigate(event.state);
};
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