var shortcutManager = {
    hadShortcut: false,
    createShortcut: function(title, base64icon, dataUrl) {
        return false;
    },
    hasShortcutCalled: function() {
        return false;
    }
};

var app = angular.module('app', ['ngTouch', 'pr.longpress']).service('currentTag', function(){
    var currentTag = 'all';
    return {
        getCurrentTag: function() {
            return currentTag;
        },
        setCurrentTag: function(tag){
            currentTag = tag;
        }
    };
});

if (typeof hostApi == 'undefined' || null == hostApi) {
    hostApi = 'http://'+window.location.host;
}
app.controller('appCtrl', function ($scope, $http, $timeout, $window, currentTag) {

    if (shortcutManager.hadShortcut) {
        return;
    }

    $scope.$parent.recipes = [];
    $scope.$parent.parametersVisible = 0;
    $scope.hostApi = hostApi;
    $scope.currentTag = currentTag;
    $scope.tags = [];


    try {
        $scope.recipes = JSON.parse(window.localStorage.getItem("recipes"));
        if (!$scope.$parent.recipes) {
            $scope.$parent.recipes = [];
        }
        $scope.tags = JSON.parse(window.localStorage.getItem("tags"));
        if (!$scope.$parent.tags) {
            $scope.$parent.tags = [];
        }
    } catch(e) {
        $scope.$parent.recipes = [];
    }

    $scope.addShortcut = function(title, base64icon, dataUrl) {
        shortcutManager.createShortcut(title, base64icon, dataUrl);
    }

    $scope.$parent.getRecipes = function(){

        if (typeof hostApi == 'undefined' || null == hostApi) {
            hostApi = 'http://'+window.location.host;
        }

        $http.get(hostApi+'/recipes?format=json&origin='+device.platform).then(function(r){
            var newTags = ['all'];
            for (var i=0; i < r.data.length; i++) {
                for (var i=0; i < r.data.length; i++) {
                    var recipe = r.data[i];
                    if (typeof recipe.tags == 'undefined') {
                        recipe.tags = [];
                    }
                    for (var j=0; j < recipe.tags.length; j++) {
                        if (newTags.indexOf(recipe.tags[j]) < 0) {
                        newTags.push(recipe.tags[j]);
                    }
                    }
                }
            }
            $scope.$parent.tags = newTags;
            $scope.$parent.recipes = r.data;

            try {
                window.localStorage.setItem("recipes", JSON.stringify(r.data));
                window.localStorage.setItem("tags", JSON.stringify(newTags));
            } catch(e) {}
        });

        return $scope.recipes;
    };

    $scope.execRecipe = function(recipe){
        recipe.runing = true;
        recipe.error = false;
        var actions = new Array;

        $http.get(hostApi+recipe.url).then(function(r){
            recipe.runing = false;
        }, function(){
            recipe.runing = false;
            recipe.error = true;
        });


        if (device.platform == 'Android') {
            if (recipe.state == 'on') {
                if (typeof recipe.actions.off != 'undefined') {
                    actions = actions.concat(recipe.actions.off);
                }
            } else {
                if (typeof recipe.actions.on != 'undefined') {
                    actions = actions.concat(recipe.actions.on);
                }
            }
            if (typeof recipe.actions.each_time != 'undefined') {
                actions = actions.concat(recipe.actions.each_time);
            }


            for (var i=0; i < actions.length; i++) {
                var action = actions[i];
                var actionsInfos = action.split(':');
                if (actionsInfos[0] == 'android' && actionsInfos[1] == 'openApp') {
                    try {
                        startApp.set({
                            "package": actionsInfos[2]
                        }).start();
                    } catch (e) {}
                }
            }
        }

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

    $scope.$parent.getRecipes();
    var countUp = function() {
        $scope.$parent.getRecipes();
        $timeout(countUp, 60000);
    }
    $timeout(countUp, 1000);
});


$(document).ready(function(){
    $('#recipesContainer').height($(window).height());
});
