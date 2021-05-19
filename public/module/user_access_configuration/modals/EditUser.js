define([
    'app'
], function (app) {
    app.factory('EditUserFactory', [
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

    app.service('EditUserService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/UserAccessConfigurationService.php/saveEditUser', data);
            }

        }
    ]);

    app.controller('EditUserController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditUserFactory',
        'EditUserService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditUser');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                
            }

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            $scope.changeAcctType = function(access){
                console.log(access);
                if (access == 'super') {
                    $scope.editUser.chk_super = true;
                    $scope.editUser.chk_admin = false;
                    $scope.editUser.chk_treas = false;
                    $scope.editUser.chk_acctg = false;
                } else if (access == 'admin') {
                    $scope.editUser.chk_admin = true;
                    $scope.editUser.chk_super = false;
                    $scope.editUser.chk_treas = false;
                    $scope.editUser.chk_acctg = false;
                } else if (access == 'treas') {
                    $scope.editUser.chk_treas = true;
                    $scope.editUser.chk_super = false;
                    $scope.editUser.chk_admin = false;
                    $scope.editUser.chk_acctg = false;

                } else if (access == 'acctg') {
                    $scope.editUser.chk_acctg = true;
                    $scope.editUser.chk_super = false;
                    $scope.editUser.chk_admin = false;
                    $scope.editUser.chk_treas = false;
                }
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this new user?",
                        function () {
                            blocker.start();
    
                            Service.save($scope.editUser).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("User successfully added!");
        
                                    $uibModalInstance.close(res.data.rowData);
                                    blocker.stop();
                                } else {
                                    Alertify.error("An error occurred while saving! Please contact the administrator.");
                                    blocker.stop();
                                }
                            });
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.editUser = ParamData.data;
                $scope.editUser.chk_super = ($scope.editUser.access_type == 1) ? true : false;
                $scope.editUser.chk_admin = ($scope.editUser.access_type == 2) ? true : false;
                $scope.editUser.chk_treas = ($scope.editUser.access_type == 3) ? true : false;
                $scope.editUser.chk_acctg = ($scope.editUser.access_type == 4) ? true : false;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
