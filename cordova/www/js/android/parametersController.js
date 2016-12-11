app.controller('parametersCtrl', function ($scope, $http, $timeout, $window) {
    var permanentStorage = window.localStorage;

    $scope.hostName = window.localStorage.getItem("host");
    hostApi = $scope.hostName;

    if (!$scope.hostName) {
        $scope.hostName = 'http://127.0.0.1';
    }

    $scope.setHost = function(host) {
        host = 'http://'+host.replace('http://', '');
        window.localStorage.setItem("host", host);
        $scope.hostName = host;
        hostApi = $scope.hostName;
        $scope.close();

    };


    $scope.close = function()
    {
        $scope.$parent.parametersVisible = 0;
    };
});
