var app = angular.module('app', ['ngTouch']);
if (typeof hostApi == 'undefined') {
    hostApi = 'http://'+window.location.hostname;
}
app.controller('appCtrl', function ($scope, $http, $timeout, $window) {
    $scope.recipes = [];
    $scope.$parent.parametersVisible = 0;

    $scope.getRecipes = function(){
        $http.get(hostApi+'/recipes?format=json&origin='+device.platform).then(function(r){
            $scope.recipes = r.data;
            console.log($scope.recipes);
        });

        return $scope.recipes;
    };

    $scope.execRecipe = function(recipe){
        recipe.runing = true;
        recipe.error = false;
        $http.get(hostApi+recipe.url).then(function(r){
            recipe.runing = false;
            var actions = r.data.actions;
            for (var action in actions) {
                if (actions.hasOwnProperty(action)) {
                    if (!actions[action]) {
                        recipe.error = true;
                    }
                    if (device.platform == 'android') {
                        var actionsInfos = actions[action].split(':');
                        var provider = actionInfos[0];
                        var method = actionInfos[1];
                        var arg = actionInfos[2];

                        if ('openApp' == method) {
                            var sApp = startApp.set({
                                "application": arg
                            });
                            sApp.start(function(){}, function(){});
                        }
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
    var countUp = function() {
        $scope.getRecipes();
        $timeout(countUp, 60000);
    }
    $timeout(countUp, 1000);
});
