angular.module('starter.controllers', [])

.controller('AskCtrl', function($scope, $http) {
  $scope.questionText = '';
  $scope.askQuestion = function() {

    var data = $.param({
        user_id: 1,
        text: $("#questionText").val()
    });

    var config = {
        headers : {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
        }
    };

    $http.post('http://www.plagosus.net/pub/why-not/questions.php?action=addQuestion', data, config).then(
       function(response){
          window.location.href = '#/tab/home';
       }, 
       function(response){
         console.log('gresks');
       }
    );
  };


})

.controller('HomeCtrl', function($scope, Chats, $http) {
  // With the new view caching in Ionic, Controllers are only called
  // when they are recreated or on app start, instead of every page change.
  // To listen for when this page is active (for example, to refresh data),
  // listen for the $ionicView.enter event:
  //
  //$scope.$on('$ionicView.enter', function(e) {
  //});

  /*$http.get('questions.json').success(function(data){
    $scope.questions = data;
  });*/
  $http.get('http://www.plagosus.net/pub/why-not/questions.php?action=getQuestions').success(function(data){
    $scope.questions = data;
  });

  $scope.chats = Chats.all();
  $scope.remove = function(chat) {
    Chats.remove(chat);
  };
})

.controller('AnswersCtrl', function($scope, $stateParams, Chats, $http) {
  //$scope.chat = Chats.get($stateParams.chatId);
  $scope.answers = $stateParams.questionId;

  $http.get('http://www.plagosus.net/pub/why-not/questions.php?action=getQuestions').success(function(data){
    angular.forEach(data, function(value, key) {
      if (value.id == $stateParams.questionId) {
        $scope.question = value;
      }
    });
  });

  $scope.sendAnswer = function() {

    var data = $.param({
        question_id: $stateParams.questionId,
        user_id: 1,
        text: $("#answerText").val()
    });

    var config = {
        headers : {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
        }
    };


    $http.post('http://www.plagosus.net/pub/why-not/questions.php?action=addAnswer', data, config).then(
       function(response){
            $("#answerText").val('');
            $http.get('http://www.plagosus.net/pub/why-not/questions.php?action=getQuestions').success(function(data){
              angular.forEach(data, function(value, key) {
                if (value.id == $stateParams.questionId) {
                  $scope.question = value;
                }
              });
            });
       }, 
       function(response){
         console.log('greska');
       }
    );
  };

})

.controller('SettingsCtrl', function($scope) {
  $scope.settings = {
    enableFriends: true
  };
});
