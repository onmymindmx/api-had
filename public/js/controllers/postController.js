var post = angular.module('PostCtrl', []);

post.controller('PostController', function($scope, PostCRUD)
{
    var getPosts = PostCRUD.all();
    getPosts.success(function(response){
        $scope.posts = response;
    });

    $scope.submit = function(){
        var request = PostCRUD.create($scope.new);
        request.success(function(response){
            $scope.flash = response.status;
        })
    };

    $scope.remove = function(id, index){
        var removePost = PostCRUD.delete(id);
        removePost.success(function(response){
            $scope.flash = response.status;
            $scope.posts.splice(index, 1);
        })
    }
});

post.controller('PostEditController', function($scope, $routeParams, PostCRUD)
{
   var getPost = PostCRUD.get($routeParams.id);
    getPost.success(function(response){
        $scope.post = response;
    });

    $scope.submit = function(){
        var request = PostCRUD.update($routeParams.id, $scope.post);
        request.success(function(response){
            $scope.flash = response.status;
        })
    };

});