var postscrud = angular.module('PostCRUDSrvc', []);

postscrud.factory("PostCRUD", function ($http) {
   return{
       all: function(){
           var request = $http({method:'GET', url:'api/posts'});
           return request;
       },
       create: function(data){
           var request = $http({method:'GET', url:'api/posts/create', params:data})
           return request;
       },
       get: function(id){
           var request = $http({method:'GET', url:'api/posts/'+id});
           return request;
       },
       update: function(id, data){
           var request = $http({method:'PUT', url:'api/posts/'+id, params:data});
           return request;
       },
       delete: function(id){
           var request = $http({method:'DELETE', url:'api/posts/'+id});
           return request;
       }
   }
});