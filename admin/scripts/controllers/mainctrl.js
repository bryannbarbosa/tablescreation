app.controller('mainCtrl', function($scope, APIService, $location, $window){

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
    setTimeout($window.location.reload(), 2000);
  }

  $scope.insertProfession = function (id_table, profession) {
    profession = profession.toLowerCase();
    profession = profession.replace(/[`~!@#´$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
    let data = {id_table: id_table, profession: profession};
    console.log(APIService.connect('profession').save(angular.copy(data)));
    setTimeout($window.location.reload(), 2000);
  }

  $scope.removeProfession = function(i, j) {
    let data = {id_table: i, id_profession: j};
    console.log(APIService.connect('profession/remove').save(data));
    setTimeout($window.location.reload(), 2000);
  }

  $scope.removeTdInitial = function (i, j, k) {
    $scope.tables[i].tbody_tr[j].tds.splice(k, 1);
  }

  $scope.removeTd = function (i, j, k, id) {
    $scope.tables[i].tbody_tr[j].tds.splice(k, 1);
    APIService.connect('tables/td/delete/' + id).remove();
    setTimeout($window.location.reload(), 2000);

  }

  $scope.saveTd = function(i, j, k, id) {
    var value = $scope.tables[i].tbody_tr[j].tds[k].td_value;
    APIService.connect('tables/td/update/' + id).save({value: value});
    setTimeout($window.location.reload(), 2000);
  }

  $scope.removeTdInitial = function (i, j, k) {
    $scope.tables[i].tbody_tr[j].tds.splice(k, 1);
  }

  $scope.removeTh = function(i, j, k, id) {
    APIService.connect('tables/th/delete/' + id).remove();
    $scope.tables[i].thead_tr[j].ths.splice(k, 1);
    setTimeout($window.location.reload(), 2000);
  }

  $scope.removeThInitial = function(i, j, k, id) {
    $scope.tables[i].thead_tr[j].ths.splice(k, 1);
  }

  $scope.saveTh = function(i, j, k, id) {
    var value = $scope.tables[i].thead_tr[j].ths[k].th_value;
    console.log(value);
    APIService.connect('tables/th/update/' + id).save({value: value});
    setTimeout($window.location.reload(), 2000);
  }

  $scope.insertHeadTr = function(i, table_id) {
    $scope.tables[i].thead_tr.push({ths: [{th_value: 'Nova Coluna'}]});
    console.log(APIService.connect('tables/thead/tr/create').save({id_table: table_id}));
    setTimeout($window.location.reload(), 2000);
  }

  $scope.insertHeadTrInitial = function(i) {
    $scope.tables[i].thead_tr.push({ths: [{th_value: 'Coluna Inicial'}]});
  }

  $scope.insertHeadTh = function(i, j, id) {
    console.log(APIService.connect('tables/th/create').save({id_tr: id, value: 'Nova Coluna'}));
    $scope.tables[i].thead_tr[j].ths.push({th_value: 'Nova Coluna'});
    setTimeout($window.location.reload(), 2000);

  }

  $scope.insertHeadThInitial = function(i, j) {
    $scope.tables[i].thead_tr[j].ths.push({th_value: 'Nova Coluna'});
  }

  $scope.insertBodyTr = function(i, table_id) {
    $scope.tables[i].tbody_tr.push({tds: [{td_value: 'Novo valor'}]});
    console.log(APIService.connect('tables/tbody/tr/create').save({id_table: table_id}));
    setTimeout($window.location.reload(), 2000);
  }

  $scope.insertBodyTrInitial = function(i) {
    $scope.tables[i].tbody_tr.push({tds: [{td_value: 'Novo valor'}]});
  }

  $scope.insertBodyTd = function(i, j, id) {
    console.log(APIService.connect('tables/td/create').save({id_tr: id, value: 'Novo Valor'}));
    $scope.tables[i].tbody_tr[j].tds.push({td_value: 'Novo valor'});
    setTimeout($window.location.reload(), 2000);
  }

  $scope.insertBodyTdInitial = function(i, j) {
    $scope.tables[i].tbody_tr[j].tds.push({td_value: 'Novo valor'});
  }

  $scope.check = function() {
    console.log($scope.tables);
  }

  $scope.saveTable = function() {
    APIService.connect('tables').save(angular.copy($scope.tables));
    setTimeout($window.location.reload(), 2000);
  }

});
