var shortcutManager = {
    createShortcut: function(title, base64icon, dataUrl) {
        return false;
    },
    hasShortcutCalled: function() {
        return false;
    }
};

var app = angular.module('app', ['ngTouch']).service('currentTag', function(){
    var currentTag = 'all';
    return {
        getCurrentTag: function() {
            return currentTag;
        },
        setCurrentTag: function(tag){
            currentTag = tag;
        }
    };
}).directive('onLongPress', function($timeout) {
	return {
		restrict: 'A',
		link: function($scope, $elm, $attrs) {
			$elm.bind('touchstart', function(evt) {
				// Locally scoped variable that will keep track of the long press
				$scope.longPress = true;

				// We'll set a timeout for 600 ms for a long press
				$timeout(function() {
					if ($scope.longPress) {
						// If the touchend event hasn't fired,
						// apply the function given in on the element's on-long-press attribute
						$scope.$apply(function() {
							$scope.$eval($attrs.onLongPress)
						});
					}
				}, 600);
			});

			$elm.bind('touchend', function(evt) {
				// Prevent the onLongPress event from firing
				$scope.longPress = false;
				// If there is an on-touch-end function attached to this element, apply it
				if ($attrs.onTouchEnd) {
					$scope.$apply(function() {
						$scope.$eval($attrs.onTouchEnd)
					});
				}
			});
		}
	};
});

if (typeof hostApi == 'undefined' || null == hostApi) {
    hostApi = 'http://'+window.location.host;
}
app.controller('appCtrl', function ($scope, $http, $timeout, $window, currentTag) {
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
