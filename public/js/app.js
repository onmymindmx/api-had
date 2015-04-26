var app = angular.module('blogApp', ['ngRoute', 'LoginCtrl', 'PostCtrl', 'AuthSrvc', 'PostCRUDSrvc']);

app.run(function(){

});

// This will handle all of our routing
app.config(function($routeProvider, $locationProvider){
    $routeProvider
        .when('/', {
            templateUrl:'js/templates/login.html',
            controller: 'LoginController'
        })
        .when('/dashboard', {
            templateUrl:'js/templates/dashboard.html',
            controller: 'PostController'
        })
        .when('/add', {
            templateUrl: 'js/templates/add.html',
            controller: 'PostController'
        })
        .when('/edit/:id', {
            templateUrl: 'js/templates/edit.html',
            controller: 'PostEditController'
        })

});