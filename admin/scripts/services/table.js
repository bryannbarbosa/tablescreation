app.factory('APIService', function($resource) {

  return {
    connect: function(params) {
      let BaseUrl = 'http://localhost:8000/public/api/';
      return $resource(BaseUrl + params);
    }
  };
});