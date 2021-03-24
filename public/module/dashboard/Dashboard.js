define([
    'app'
], function(app) {
    app.factory('dashboardFactory', [
        'alertify',
        function(alertify) {
            var Factory = {};

            /**
             * `autoloadSettings` autoload params
             * @return {[type]}
             */
            Factory.autoloadSettings = function() {
                // alertify
                alertify.logPosition('bottom right');
                alertify.theme('')
            };

            /**
             * `templates` Modal templates.
             * @type {Array}
             */
            Factory.templates = function() {
                var templates;

                templates = [
                    // 'module/dashboard/modals/sub_menu.html',
                ];

                return templates;
            };

            return Factory;
        }
    ]);

    app.service('dashboardService', [
        '$http',
        function($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            // _this.getDetails = function() {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/Dashboard/DashboardService.php/getDetails');
            // };
        }
    ]);

    app.controller('DashboardController', [
        '$scope',
        '$filter',
        '$uibModal',
        '$window',
        '$timeout',
        'dashboardFactory',
        'dashboardService',
        function($scope, $filter, $uibModal, $window, $timeout, Factory, Service) {
            var _init, _loadDetails;

            /**
             * `_loadDetails` load needed details in `dashboard`.
             * @return {[mixed]}
             */
            _loadDetails = function() {

            };

            /**
             * `_init` First things first.
             * @return {[mixed]}
             */
            _init = function() {
                // default settings
                Factory.autoloadSettings();

                $scope.global.prev_route = '/main/dashboard';
                $scope.header.title = "Dashboard";
                $scope.header.showButton = true;

                $scope.templates = Factory.templates();

                _loadDetails();
            };

            _init();
        }
    ]);
}); // end define