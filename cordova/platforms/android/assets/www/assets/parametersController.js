app.controller('parametersCtrl', function ($scope, $http, $timeout, $window, currentTag, $voice) {
    $scope.hostName = hostApi = $scope.$parent.hostName = window.localStorage.getItem("host");
    $scope.changeHost = null;
    $scope.currentTag = currentTag;

    if (!$scope.hostName) {
        $scope.$parent.parametersVisible = 1;
        if (typeof window.location.hostname != 'undefined' && window.location.hostname) {
            $scope.hostName = 'http://'+window.location.hostname;
        }
    }

    $scope.$voice = $voice;

    $scope.toggleVoiceManager = function(){
        var manager = $voice.getManager();

        if (manager.listening) {
            manager.listener.stop();
            manager.listening = false;
        } else {
            manager.listener.start();
            manager.listening = true;
        }
    }

    $scope.setHost = function(host) {
        host = 'http://'+host.replace('http://', '');
        window.localStorage.setItem("host", host);
        $scope.hostName = host;
        hostApi = $scope.hostName;
        $scope.close();
        $scope.$parent.getRecipes();
    };

    $scope.$parent.loadConfig = function() {
        if (!isReady) {
            return $timeout($scope.loadConfig, 500);
        }
        apiRecipeConfigLoaded = true;

        $http.get(hostApi+'/config').then(function(r){
            apiRecipeConfig = r.data;
            window.localStorage.setItem("config", JSON.stringify(r.data));
            $voice.setup(apiRecipeConfig);
        }, function(){
            apiRecipeConfig = JSON.parse(window.localStorage.getItem("config"));
            if (null != apiRecipeConfig) {
                $voice.setup(apiRecipeConfig);
            }
        });
    };


    $scope.close = function()
    {
        $scope.$parent.parametersVisible = 0;
    };

    $scope.loadTag = function(tag) {
        $scope.$parent.currentTag = tag;
        $scope.$parent.loadRecipes();
    };
});
