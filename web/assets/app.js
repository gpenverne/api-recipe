var app = angular.module('app', ['ngTouch']);
if (typeof hostApi == 'undefined') {
    hostApi = 'http://127.0.0.1';
}
app.controller('appCtrl', function ($scope, $http, $timeout, $window) {
    $scope.recipes = [];
    $scope.$parent.parametersVisible = 0;
    $scope.hostApi = hostApi;
    try {
        $scope.recipes = JSON.parse(window.localStorage.getItem("recipes"));
        if (!$scope.recipes) {
            $scope.recipes = [];
        }
    } catch(e) {
        $scope.recipes = [];
    }

    $scope.getRecipes = function(){
        $http.get(hostApi+'/recipes?format=json&origin='+device.platform).then(function(r){
            $scope.recipes = r.data;
            try {
                window.localStorage.setItem("recipes", JSON.stringify(r.data));
            } catch(e) {}
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
                if (device.platform == 'Android' || true) {
                    var actionsInfos = action.split(':');
                    var provider = actionsInfos[0];
                    var method = actionsInfos[1];
                    if ('android' == provider && 'openApp' == method) {
                        try {
                            var arg = actionsInfos[2];
                            startApp.set({
                                "package": arg
                            }).start();
                        } catch (e) {}
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
