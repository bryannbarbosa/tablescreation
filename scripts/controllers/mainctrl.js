app.controller('mainCtrl', function($scope, APIService){

  APIService.connect('profession').query(function(data) {
    $scope.professions = data;
  });
  console.log(APIService.connect('profession').query());

});
