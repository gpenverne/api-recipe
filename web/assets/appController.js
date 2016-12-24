app.controller('appCtrl', function ($scope, $http, $timeout, $window, currentTag) {

    if (shortcutManager.hadShortcut) {
        return;
    }

    $scope.$parent.recipes = new Array;

    $scope.$parent.parametersVisible = 0;
    $scope.hostApi = hostApi;
    $scope.currentTag = currentTag;
    $scope.tags =  new Array;


    try {
        $scope.$parent.recipes = JSON.parse(window.localStorage.getItem("recipes"));
        if (!$scope.$parent.recipes) {
            $scope.$parent.recipes = new Array;
        }
        $scope.$parent.tags = JSON.parse(window.localStorage.getItem("tags"));
        if (!$scope.$parent.tags) {
            $scope.$parent.tags = new Array;
        }
    } catch(e) {
        $scope.$parent.recipes = new Array;
    }

    $scope.$parent.addShortcut = function(recipe) {
        shortcutManager.createShortcut(JSON.parse(angular.toJson(recipe)));
    }

    $scope.$parent.getRecipes = function(){
        if (typeof hostApi == 'undefined' || null == hostApi) {
            if (window.location.host == '') {
                return;
            }
            hostApi = 'http://'+window.location.host    ;
        }
        var url = hostApi+'/recipes?format=json&origin='+device.platform;
        $http.get(url).then(function(r){
            $scope.$parent.online = true;
            var newTags = ['all'];
            var recipes = new Array;
            for (var i=0; i < r.data.length; i++) {
                var recipe = r.data[i];
                if (typeof recipe.picture == 'undefined' || !recipe.picture) {
                    continue;
                }
                if (typeof recipe.tags == 'undefined') {
                    recipe.tags = new Array;
                }
                for (var j=0; j < recipe.tags.length; j++) {
                    if (newTags.indexOf(recipe.tags[j]) < 0) {
                        newTags.push(recipe.tags[j]);
                    }
                }
                recipe.androidApp = null;
                recipe.confirm = null;

                var actions = new Array;

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

                for (var j=0; j < actions.length; j++) {
                    var action = actions[j];
                    var actionsInfos = action.split(':');
                    if (device.platform == 'Android' && actionsInfos[0] == 'android' && actionsInfos[1] == 'openApp') {
                        recipe.androidApp = actionsInfos[2];
                    }
                    if (actionsInfos[0] == 'confirm' && actionsInfos[1] == 'confirm') {
                        recipe.confirm = actionsInfos[2];
                    }
                }
                recipe.url = hostApi + recipe.url;
                recipes.push(recipe);
            }
            $scope.$parent.tags = newTags;
            $scope.$parent.recipes = recipes;
            try {
                window.localStorage.setItem("recipes", JSON.stringify(recipes));
                window.localStorage.setItem("tags", JSON.stringify(newTags));
            } catch(e) {}

            $timeout(countUp, 60000);
        }, function(errors, status){
            $scope.$parent.online = false;
            try {
                $scope.recipes = JSON.parse(window.localStorage.getItem("recipes"));
            } catch(e){
                $scope.recipes = new Array;
            }
            $timeout(countUp, 60000);
        });


        return $scope.recipes;
    };

    $scope.execRecipe = function(recipe, forcedState){
        recipe.runing = true;
        recipe.error = false;

        if (recipe.confirm) {
            if (!confirm(recipe.confirm)) {
                recipe.runing = false;
                return ;
            }
        }

        var actions = new Array;
        var url = recipe.url;
        if (url.replace('http://') == url) {
            var url = hostApi+recipe.url;
        }
        if (typeof forcedState != 'undefined' && forcedState) {
            url += '&state=' + forcedState;
        }
        $http.get(url).then(function(r){
            recipe.runing = false;
            $scope.getRecipes();
        }, function(){
            recipe.runing = false;
            recipe.error = true;
        });

        if (recipe.androidApp) {
            handleAndroidAppLaunch(recipe.androidApp);
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

    var countUp = function() {
        $scope.$parent.getRecipes();
    }


    $timeout(countUp, 500);
});


$(document).ready(function(){
    $('#recipesContainer').height($(window).height());
});

function handleAndroidAppLaunch(appName)
{
    try {
        startApp.set({
            "package": appName
        }).start();
    } catch (e) {}
}

document.addEventListener("deviceready", onDeviceReady, false);
var voiceInitialized = null;
var isReady = device.platform == 'Android' ? false : true;

function onDeviceReady() {
    isReady = true;
    document.addEventListener("resume", onResume, false);
    onResume();
}
function onPause() {
    voiceManager.listening = false;
    if (voiceManager.listener) {
        voiceManager.listener.stop();
    }
}

function onResume() {
    if (voiceManager.listener) {
        voiceManager.listener.start();
    } if (typeof shortcutManager != 'undefined') {
        shortcutManager.execExtra();
    }
}
