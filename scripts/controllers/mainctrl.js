app.controller('mainCtrl', function($scope, APIService){

  APIService.connect('profession/distinct').query(function(data) {
    $scope.professions = data;
  });

});
