var gitApp = angular.module('gitApp', [
    'gitAppControllers'
]);

gitApp.factory('httpLoader',['$q',function($q){
    return {
        'request': function(config) {
            // do something on success
            $('#loader').show();
            return config || $q.when(config);
        },
        'requestError': function(rejection) {
            $('#loader').hide();
            return $q.reject(rejection);
        },
        'response': function(response) {
            $('#loader').hide();
            return response || $q.when(response);
        },
        'responseError': function(rejection) {
            $('#loader').hide();
            return $q.reject(rejection);
        }
    }
}]);

gitApp.config(['$httpProvider',function($httpProvider) {
    $httpProvider.interceptors.push('httpLoader');
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

gitApp.filter('shortHash', function() {
    return function(input) {
        if (input == null || input == "" || input == undefined) {
            return "";
        }
        
        return input.substring(0,6);
    }
});
