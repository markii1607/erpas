define([
    'app'
], function (app) {
    app.factory('AddUserFactory', [
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

    app.service('AddUserService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/UserAccessConfigurationService.php/saveNewUser', data);
            }

        }
    ]);

    app.controller('AddUserController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddUserFactory',
        'AddUserService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddUser');

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
                    $scope.addUser.chk_super = true;
                    $scope.addUser.chk_admin = false;
                    $scope.addUser.chk_treas = false;
                    $scope.addUser.chk_acctg = false;
                } else if (access == 'admin') {
                    $scope.addUser.chk_admin = true;
                    $scope.addUser.chk_super = false;
                    $scope.addUser.chk_treas = false;
                    $scope.addUser.chk_acctg = false;
                } else if (access == 'treas') {
                    $scope.addUser.chk_treas = true;
                    $scope.addUser.chk_super = false;
                    $scope.addUser.chk_admin = false;
                    $scope.addUser.chk_acctg = false;

                } else if (access == 'acctg') {
                    $scope.addUser.chk_acctg = true;
                    $scope.addUser.chk_super = false;
                    $scope.addUser.chk_admin = false;
                    $scope.addUser.chk_treas = false;
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
    
                            Service.save($scope.addUser).then( function (res) {
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
                
                $scope.addUser = {
                    chk_super : false,
                    chk_admin : false,
                    chk_treas : false,
                    chk_acctg : false,
                };

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
