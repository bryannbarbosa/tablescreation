app.controller('agesCtrl', function($scope, APIService, $route, $window) {
  $scope.ages = APIService.connect('ages').query();
  $scope.relation = APIService.connect('ages/tbody/tr').query();
  APIService.connect('tables').query(function(data) {
    $scope.tables = data;
  });

  $scope.showInfo = function() {
    for (var i = 0; i < $scope.tables.length; i++) {
      for (var j = 0; j < $scope.tables[i].tbody_tr.length; j++) {
        for (var k = 0; k < $scope.relation.length; k++) {
          if ($scope.tables[i].tbody_tr[j].id == $scope.relation[k].id_tbody_tr) {
            $scope.tables[i].tbody_tr[j].selected = true;
            $scope.tables[i].tbody_tr[j].age = $scope.relation[k].id_age;
          }
        }
      }
    }
  }
  $scope.insertAge = function(age) {
    let initial_age = age.age_initial;
    let final_age = age.age_final;
    let age_name = initial_age.toString() + final_age.toString();
    console.log(APIService.connect('ages/insert').save({age_name: age_name, age_initial: initial_age, age_final: final_age}));
    $scope.ages.push(angular.copy(age));
    setTimeout($window.location.reload(), 2000);

  }
  $scope.deleteAge = function(id, i) {
    console.log(APIService.connect('ages/delete/' + id).remove());
    $scope.ages.splice(i, 1);
    setTimeout($window.location.reload(), 2000);
  }
  $scope.insertTrAge = function(id_age, id_tr) {
    let age = id_age;
    let tr = id_tr;
    console.log(APIService.connect('ages/tbody/tr/insert').save({id_age: age, id_tbody_tr: tr}));
  }

  $scope.deleteTrAge = function(id_age, id_tr) {
    let age = id_age;
    let tr = id_tr;
    console.log(APIService.connect('ages/tbody/tr/delete').save({id_age: age, id_tr: tr}));
    setTimeout($window.location.reload(), 2000);
  }

});
