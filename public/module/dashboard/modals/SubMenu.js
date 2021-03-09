define([
    'app'
], function (app) {
    app.factory('subMenuFactory', [
        'alertify',
        function (alertify) {
            var Factory = {};

            /**
             * `autoloadSettings` autoload params
             * @return {[type]}
             */
            Factory.autoloadSettings = function () {
                // alertify
                alertify.logPosition('bottom right');
                alertify.theme('')
            };

            return Factory;
        }
    ]);

    app.service('subMenuService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            _this.getDetails = function (parentId) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Dashboard/SubMenuService.php/getDetails?parent_id=' + parentId);
            };
        }
    ]);

    app.controller('SubMenuController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$location',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'subMenuFactory',
        'subMenuService',
        function ($scope, $uibModalInstance, $timeout, $location, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockSubMenu');

            /**
             * `_loadDetails` Loading of first needed data from database.
             * @return {[type]} [description]
             */
            _loadDetails = function () {
                Service.getDetails(ParamData.parent_id).then( function (res) {
                    // $scope.subMenu.lists        = res.data.sub_menus;
                    $scope.subMenu.lists        = angular.copy(res.data.accessed_sub_menus);
                    $scope.subMenu.access_lists = angular.copy(res.data.accessed_sub_menus);
                });
            };

            /**
             * `closeSubMenu` Closing of modal.
             * @return {[void]}
             */
            $scope.closeSubMenu = function () {
                $uibModalInstance.dismiss();
            };

            /**
             * `loadModule` Redirecting to module.
             * @param  {[string]} link
             * @return {[void]}
             */
            $scope.loadModule = function (id, link, mname) {
                blocker.start();

                var iFilter = $filter('filter')($scope.subMenu.access_lists, { id : id }, true);

                if (iFilter.length > 0) {
                    $timeout( function () {
                        // delete $scope.contentheader.subModuleChild;
                        $uibModalInstance.close();

                        $location.path('/main/' + link);
                    }, 1000);
                } else {
                    blocker.stop();

                    Alertify.error("Access denied. You are not authorized to access " + mname + " module.");
                }
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.subMenu = angular.copy(ParamData);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define