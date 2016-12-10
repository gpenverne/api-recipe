var app = angular.module('app', []);
app.controller('appCtrl', function ($scope, $http, $timeout, $window) {
    $scope.recipes = [];
    $scope.getRecipes = function(){
        $http.get('/?recipes').then(function(r){
            $scope.recipes = r.data.recipes;
        });

        return $scope.recipes;
    };

    $scope.execRecipe = function(recipe){
        recipe.runing = true;
        recipe.error = false;
        $http.get(recipe.url).then(function(r){
            recipe.runing = false;
            var actions = r.data.actions;
            for (var action in actions) {
                if (actions.hasOwnProperty(action)) {
                    if (!actions[action]) {
                        recipe.error = true;
                    }
                }
            }

        }, function(){
            recipe.runing = false;
            recipe.error = true;
        });

        if (typeof recipe.external != 'undefined') {
            $window.open(recipe.external);
        }
    };

    $scope.getMagicButtonAction = function() {

        $http.get('/magic-button.php?format=json').then(function(r){
            $scope.suggested_action = r.data;
        });


    }
    $scope.isLandscape = function() {
        return $(window).width() > $(window).height();
    }

    if ($scope.isLandscape()) {
        $scope.recipeWidth = $(window).height() / 4;
        $scope.marginLeft = $scope.recipeWidth / 3;
    } else {
        $scope.recipeWidth = $(window).width() / 4;
        $scope.marginLeft = $scope.recipeWidth / 3;
    }

    $scope.getRecipes();
    $scope.getMagicButtonAction();
    var countUp = function() {
        $scope.getRecipes();
        $scope.getMagicButtonAction();
        $timeout(countUp, 60000);
    }

    $timeout(countUp, 60000);
});
