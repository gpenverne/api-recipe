app.controller('parametersCtrl', function ($scope, $http, $timeout, $window) {
    var permanentStorage = window.localStorage;

    $scope.hostName = $scope.$parent.hostName = window.localStorage.getItem("host");
    if (!$scope.hostName) {
        $scope.$parent.parametersVisible = 1;
    }
    hostApi = $scope.hostName;

    if (!$scope.hostName) {
        $scope.hostName = 'http://'+window.location.hostname;
    }

    $scope.setHost = function(host) {
        host = 'http://'+host.replace('http://', '');
        window.localStorage.setItem("host", host);
        $scope.hostName = host;
        hostApi = $scope.hostName;
        $scope.close();
        document.location.reload();

    };


    $scope.close = function()
    {
        $scope.$parent.parametersVisible = 0;
    };
});
