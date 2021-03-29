define([
    'app'
], function (app) {
    app.factory('accessFactory', [
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

    app.service('accessService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails`
             * @return {[type]} [description]
             */
            _this.getDetails = function (userId) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/AccessService.php/getDetails?user_id=' + userId);
            }

            /**
             * `save` Query string that will update details.
             * @return {[query]}
             */
            _this.save = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/AccessService.php/saveAccess', input);
            };
        }
    ]);

    app.controller('AccessController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'blockUI',
        'alertify',
        'subParamData',
        'accessFactory',
        'accessService',
        function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, SubParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAccessForm');

            /**
             * `_loadDetails` Fetching of first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                Service.getDetails(SubParamData.user_id).then( function (res) {
                    $scope.menus = angular.copy(res.data.menus);
                });
            };

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to configure access?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                $timeout( function () {
                                    $scope.access.type    = angular.copy(SubParamData.mType);
                                    $scope.access.user_id = angular.copy(SubParamData.user_id);

                                    Service.save($scope.access).then( function (res) {
                                        if (res.data.status == true) {
                                            Alertify.success("Access to module successfully configured!");

                                            $scope.access.id      = res.data.id;
                                            $scope.access.name    = angular.copy($scope.access.menu.name);
                                            $scope.access.menu_id = angular.copy($scope.access.menu.id);
                                            $scope.access.parent  = angular.copy($scope.access.menu.parent);

                                            $uibModalInstance.close($scope.access);
                                            blocker.stop();
                                        } else {
                                            Alertify.error("Module access already configured!");
                                            blocker.stop();
                                        }
                                    });
                                }, 1000);
                            }
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };

            /**
             *`addMoreItems` Adding more items for infinite scroll
             */
            $scope.addMoreItems = function () {
                $scope.infiniteScroll.currentItems += $scope.infiniteScroll.numToAdd;
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                // infinite scroll settings
                $scope.infiniteScroll              = {};
                $scope.infiniteScroll.numToAdd     = 20;
                $scope.infiniteScroll.currentItems = 20;

                $scope.form                = {};
                $scope.access              = (SubParamData.mType == 'add') ? {'office' : 'ho'} : SubParamData.rowData;
                $scope.accessConf          = angular.copy(SubParamData);
                $scope.accessConf.mLabel   = (SubParamData.mType == 'add') ? 'new'  : 'edit';
                $scope.accessConf.btnLabel = (SubParamData.mType == 'add') ? 'Save' : 'Update';

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
