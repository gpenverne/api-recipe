app.controller('parametersCtrl', function ($scope, $http, $timeout, $window, currentTag) {
    if (shortcutManager.hadShortcut) {
        return;
    }
    $scope.hosts = window.localStorage.getItem("hosts");
    if ($scope.hosts) {
        $scope.hosts = JSON.parse($scope.hosts);
    } else {
        $scope.hosts = {};
    }
    $scope.changeHost = null;

    $scope.currentTag = currentTag;

    $scope.hostName = $scope.$parent.hostName = window.localStorage.getItem("host");
    if (!$scope.hostName) {
        $scope.$parent.parametersVisible = 1;
    }
    hostApi = $scope.hostName;

    if (!$scope.hostName) {
        $scope.hostName = 'http://'+window.location.hostname;
    }
    $scope.setHost = function(host, name) {

        if (typeof name == 'undefined') {
            name = host;
        }

        if (typeof host == 'object') {
            name = host.name;
            host = host.host;
        }

        host = 'http://'+host.replace('http://', '');
        window.localStorage.setItem("host", host);
        $scope.hostName = host;
        hostApi = $scope.hostName;

        $scope.hosts[host] = {'name': name, 'host': host};
        window.localStorage.setItem("hosts", JSON.stringify($scope.hosts));

        $scope.close();
        document.location.reload();

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
