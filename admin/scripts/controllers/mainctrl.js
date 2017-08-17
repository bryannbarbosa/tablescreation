app.controller('mainCtrl', function($scope, APIService, $location){

  $scope.isActive = function (viewLocation) {
        return viewLocation === $location.path();
  };

  APIService.connect('tables').query(function(data) {
    $scope.tables = data;
  });

  APIService.connect('profession').query(function(data) {
    $scope.professions = data;
  });

  $scope.insertTable = function() {
    $scope.tables.push({id: null, agreement_name: null, agreement_image_url: null, open_agreement: null, thead_tr: [], tbody_tr: []});
  }

  $scope.removeTable = function(i, j) {
    $scope.tables.splice(i, 1);
    console.log(APIService.connect('tables/delete').save({id: j}));
  }

  $scope.insertProfession = function (id_table, profession) {
    profession = profession.toLowerCase();
    profession = profession.replace(/[`~!@#´$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
    let data = {id_table: id_table, profession: profession};
    console.log(APIService.connect('profession').save(angular.copy(data)));
  }

  $scope.removeProfession = function(i, j) {
    let data = {id_table: i, id_profession: j};
    console.log(APIService.connect('profession/remove').save(data));
  }

  $scope.removeTdInitial = function (i, j, k) {
    $scope.tables[i].tbody_tr[j].tds.splice(k, 1);
  }

  $scope.removeTd = function (i, j, k, id) {
    $scope.tables[i].tbody_tr[j].tds.splice(k, 1);
    console.log(APIService.connect('tables/td/delete/' + id).remove());

  }

  $scope.saveTd = function(i, j, k, id) {
    var value = $scope.tables[i].tbody_tr[j].tds[k].td_value;
    console.log(APIService.connect('tables/td/update/' + id).save({value: value}));
  }

  $scope.removeTdInitial = function (i, j, k) {
    $scope.tables[i].tbody_tr[j].tds.splice(k, 1);
  }

  $scope.removeTh = function(i, j, k, id) {
    console.log(APIService.connect('tables/th/delete/' + id).remove());
    $scope.tables[i].thead_tr[j].ths.splice(k, 1);
  }

  $scope.removeThInitial = function(i, j, k, id) {
    $scope.tables[i].thead_tr[j].ths.splice(k, 1);
  }

  $scope.saveTh = function(i, j, k, id) {
    var value = $scope.tables[i].thead_tr[j].ths[k].th_value;
    console.log(value);
    APIService.connect('tables/th/update/' + id).save({value: value});
  }

  $scope.insertHeadTr = function(i) {
    $scope.tables[i].thead_tr.push({ths: [{th_value: 'Coluna Inicial'}]});
    console.log($scope.tables[i]);
  }

  $scope.insertHeadTh = function(i, j, id) {
    console.log(APIService.connect('tables/th/create').save({id_tr: id, value: 'Nova Coluna'}));
    $scope.tables[i].thead_tr[j].ths.push({th_value: 'Nova Coluna'});

  }

  $scope.insertHeadThInitial = function(i, j) {
    $scope.tables[i].thead_tr[j].ths.push({th_value: 'Nova Coluna'});
  }

  $scope.insertBodyTr = function(i) {
    $scope.tables[i].tbody_tr.push({tds: [{td_value: 'Novo valor', width: 100}]});
  }

  $scope.insertBodyTd = function(i, j, id) {
    console.log(APIService.connect('tables/td/create').save({id_tr: id, value: 'Novo Valor'}));
    $scope.tables[i].tbody_tr[j].tds.push({td_value: 'Novo valor', width: 100});
  }

  $scope.insertBodyTdInitial = function(i, j) {
    $scope.tables[i].tbody_tr[j].tds.push({td_value: 'Novo valor', width: 100});
  }

  $scope.check = function() {
    console.log($scope.tables);
  }

  $scope.saveTable = function() {
    console.log(APIService.connect('tables').save(angular.copy($scope.tables)));
  }

});
