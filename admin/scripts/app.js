var app = angular.module('agreementApp', ['ngRoute', 'ui.bootstrap', 'ngAnimate', 'ngResource']);

app.config(function($routeProvider, $locationProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "views/main.html"
    })
    .when("/idades", {
        templateUrl : "views/ages.html"
    });
    $locationProvider.hashPrefix('');
});
