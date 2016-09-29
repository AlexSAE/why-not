angular.module('starter.controllers', [])

.controller('AskCtrl', function($scope) {})

.controller('HomeCtrl', function($scope, Chats, $http) {
  // With the new view caching in Ionic, Controllers are only called
  // when they are recreated or on app start, instead of every page change.
  // To listen for when this page is active (for example, to refresh data),
  // listen for the $ionicView.enter event:
  //
  //$scope.$on('$ionicView.enter', function(e) {
  //});

  $http.get('questions.json').success(function(data){
    $scope.questions = data;
  });
  /*$http.get('http://localhost/why-not/api/questions.php').success(function(data){
    $scope.questions = data;
  });*/

  $scope.chats = Chats.all();
  $scope.remove = function(chat) {
    Chats.remove(chat);
  };
})

.controller('AnswersCtrl', function($scope, $stateParams, Chats, $http) {
  //$scope.chat = Chats.get($stateParams.chatId);
  $scope.answers = $stateParams.questionId;


  $http.get('questions.json').success(function(data){
    angular.forEach(data, function(value, key) {
      if (value.id == $stateParams.questionId) {
        $scope.question = value;
      }
    });
  });
  /*$http.get('http://localhost/why-not/api/questions.php').success(function(data){
    angular.forEach(data, function(value, key) {
      if (value.id == $stateParams.questionId) {
        $scope.question = value;
      }
    });
  });*/

})

.controller('SettingsCtrl', function($scope) {
  $scope.settings = {
    enableFriends: true
  };
});
