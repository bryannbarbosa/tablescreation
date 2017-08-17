app.controller('agesCtrl', function($scope, APIService){
  $scope.ages = APIService.connect('ages').query();
  APIService.connect('tables').query(function(data) {
    $scope.tables = data;
  });

  $scope.showInfo = function(el) {
    console.log(el);
  }

  $scope.insertAge = function(age) {
    let initial_age = age.age_initial;
    let final_age = age.age_final;
    let age_name = initial_age.toString() + final_age.toString();
    console.log(APIService.connect('ages/insert').save({age_name: age_name,
    age_initial: initial_age, age_final: final_age}));
    $scope.ages.push(angular.copy(age));
  }
  $scope.deleteAge = function(i, id) {
    console.log(APIService.connect('ages/delete/' + id).remove());
    $scope.ages.splice(i, 1);
  }
});
