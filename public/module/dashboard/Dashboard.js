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
                    'module/dashboard/modals/sub_menu.html',
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
            _this.getDetails = function() {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Dashboard/DashboardService.php/getDetails');
            };
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
                Service.getDetails().then(function(res) {
                    $scope.dashboardInfo.is_signatory = res.data.is_signatory;
                    $scope.menus                      = res.data.menus;
                    $scope.user_id                    = res.data.user_id;

                    $scope.dbMenu.ho = $filter('filter')(res.data.menus, {
                        'office': 'ho',
                    }, true);

                    $scope.dbMenu.fo = $filter('filter')(res.data.menus, {
                        'office': 'fo',
                    }, true);
                });
            };

            /**
             * `loadChildMenus` loading of child or sub menus of parent menus.
             * @param  {[strig]} type
             * @param  {[strig]} parentId
             * @param  {[strig]} name
             * @return {[route]}
             */
            $scope.loadChildMenus = function(type, parentId, name) {
                var paramData, modalInstance;

                paramData = {
                    'type': type,
                    'parent_id': parentId,
                    'name': name
                };

                modalInstance = $uibModal.open({
                    animation: true,
                    keyboard: false,
                    backdrop: 'static',
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'sub_menu.html',
                    controller: 'SubMenuController',
                    size: 'lg',
                    resolve: {
                        paramData: function() {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function(res) {
                    console.log(res);
                }, function(res) {
                    // Result when modal is dismissed
                });
            };

            /**
             * `_init` First things first.
             * @return {[mixed]}
             */
            _init = function() {
                // default settings
                Factory.autoloadSettings();

                $scope.global.prev_route = '/main/dashboard';
                $scope.contentheader.title = 'Dashboard';

                $scope.templates = Factory.templates();
                $scope.dbMenu = {};
                $scope.dashboardInfo = {};

                // console.log($scope.templates);
                _loadDetails();
            };

            _init();
        }
    ]);
}); // end define