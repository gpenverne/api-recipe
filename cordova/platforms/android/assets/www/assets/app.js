var apiRecipeConfig = null;
var waitingForWords = false;

var shortcutManager = {
    hadShortcut: false,
    createShortcut: function(recipe) {
        return false;
    },
    hasShortcutCalled: function() {
        return false;
    }
};
var voiceManager = {
    listener: null,
    listening: false,
    say: function(text){
        var msg = new SpeechSynthesisUtterance();
        var voices = window.speechSynthesis.getVoices();
        msg.volume = 1; // 0 to 1
        msg.rate = 1; // 0.1 to 10
        msg.pitch = 2; //0 to 2
        msg.text = text;
        msg.lang = 'fr-FR';
        console.log('say text: '+text);
        speechSynthesis.speak(msg);
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

    $scope.$parent.recipes = new Array;

    $scope.$parent.parametersVisible = 0;
    $scope.hostApi = hostApi;
    $scope.currentTag = currentTag;
    $scope.tags =  new Array;


    $scope.loadConfig = function() {
        if (!isReady) {
            return $timeout($scope.loadConfig, 500);
        }
        $http.get(hostApi+'/config').then(function(r){
            apiRecipeConfig = r.data;
        }, function(){
            return $timeout($scope.loadConfig, 250);
        });
    };

    $scope.onListened = function(txt){
        if (typeof apiRecipeConfig.voices.keywords == 'undefined') {
            return ;
        }
        txt = txt.toLowerCase();
        console.log(txt);
        for (var i=0; i < apiRecipeConfig.voices.keywords.length; i++) {
            var keyword = apiRecipeConfig.voices.keywords[i].toLowerCase();
            var trueText = txt.replace(keyword, '');
            if (trueText != txt) {
                $http.get(hostApi+'/voice/deduce?text='+encodeURI(trueText)).then(function(r){
                    if (r.data && r.data.recipe && r.data.voiceMessage) {
                        voiceManager.say(r.data.voiceMessage);
                    }
                });
                return ;
            }
        }
    };

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
            hostApi = 'http://'+window.location.host;
        }

        $http.get(hostApi+'/recipes?format=json&origin='+device.platform).then(function(r){
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
        }, function(r){
            try {
                $scope.recipes = JSON.parse(window.localStorage.getItem("recipes"));
            } catch(e){
                $scope.recipes = new Array;
            }
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

    $scope.$parent.getRecipes();

    var countUp = function() {
        $scope.$parent.getRecipes();
        $timeout(countUp, 60000);

    }

    var resetAudio = function() {
        if (!isReady || !apiRecipeConfig) {
            return $timeout(resetAudio, 500);
        }

        if (voiceManager.listening) {
            return false;
        }
        try {
            recognitionClass = window.webkitSpeechRecognition || window.speechRecognition || window.mozSpeechRecognition || window.webkitSpeechRecognition || SpeechRecognition;
            if (!recognitionClass) {
                alert('unable to use voice recognition');
                return false;
            }
            voiceManager.listener = new recognitionClass();
            voiceManager.listener.lang = apiRecipeConfig.voices.locale;
            voiceManager.listener.continuous = true;
            voiceManager.listener.interimResults = true;
            voiceManager.listener.onresult = function(event) {
                var interim_transcript = '';
                for (var i = event.resultIndex; i < event.results.length; ++i) {
                    var result = event.results[i];
                    if (result.isFinal) {
                        $scope.onListened(result[0].transcript);
                    }
                }
            };

            voiceManager.listener.start();
            voiceManager.listening = true;

        } catch (e) {
            voiceManager.listening = false;
            voiceManager.listener = null;
        }

    }
    $timeout(countUp, 500);
    $timeout(resetAudio, 500);
    $scope.loadConfig();
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
    if (!voiceListener) {
        return;
    }
    voiceManager.listener.stop();
}

function onResume() {
    try {
        voiceManager.listener.start();
    }catch(e){

    }
    shortcutManager.execExtra();
}
