define([
    'app'
], function (app) {
    app.factory('changePasswordFactory', [
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

    app.service('changePasswordService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/ChangePasswordService.php/getDetails');
            }

            /**
             * `chage` Query string that will update details.
             * @return {[query]}
             */
            _this.chage = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/ChangePasswordService.php/saveNewPassword', input);
            };
        }
    ]);

    app.controller('ChangePasswordController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'blockUI',
        'alertify',
        // 'md5',
        'paramData',
        'changePasswordFactory',
        'changePasswordService',
        // function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, md5, ParamData, Factory, Service) {
        function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockChangePassword');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then( function (res) {
                    // $scope.changePassInfo.password         = res.data.old_pass[0].user_pass;
                    $scope.changePassInfo.password         = '';
                    $scope.changePassInfo.cur_pass         = '';
                    $scope.changePassInfo.confirm_new_pass = '';
                    
                    blocker.stop();
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
             * `change` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.change = function (isValid) {
                if (isValid && $scope.password.approved) {
                    Alertify.confirm("Are you sure you want to change your password?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                Service.chage($scope.changePassInfo).then( function (res) {
                                    if (res.data.status == true) {
                                        Alertify.success("Password successfully changed!");

                                        $uibModalInstance.close($scope.changePassInfo);
                                        blocker.stop();
                                    } else {
                                        Alertify.error("Details already exist!");
                                        blocker.stop();
                                    }
                                });
                            }
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };
            
            /**
             * $watch watch the $scope.changePassInfo.cur_pass and run function when the scope value is changed.
             */
            $scope.$watch('changePassInfo.cur_pass', function () {
                if ($scope.changePassInfo.cur_pass == '') {
                    $scope.oldPassword.default     = true;
                    $scope.oldPassword.approved    = false;
                    $scope.oldPassword.disapproved = false;
                } else {
                    // if (md5.createHash($scope.changePassInfo.cur_pass || '') == $scope.changePassInfo.password) {
                    if ($scope.changePassInfo.cur_pass) {
                        $scope.oldPassword.default     = false;
                        $scope.oldPassword.approved    = true;
                        $scope.oldPassword.disapproved = false;
                    } else {
                        $scope.oldPassword.default     = false;
                        $scope.oldPassword.approved    = false;
                        $scope.oldPassword.disapproved = true;
                    }
                }
            });

            /**
             * $watch watch the $scope.changePassInfo.confirmPassword and run function when the scope is changed.
             */
            $scope.$watch('changePassInfo.confirm_new_pass', function () {
                if ($scope.changePassInfo.confirm_new_pass == '') {
                    $scope.password.default     = true;
                    $scope.password.approved    = false;
                    $scope.password.disapproved = false;
                } else {
                    // if ($scope.changePassInfo.new_pass == $scope.changePassInfo.confirm_new_pass) {
                    if ($scope.changePassInfo.new_pass == $scope.changePassInfo.confirm_new_pass) {
                        $scope.password.default     = false;
                        $scope.password.approved    = true;
                        $scope.password.disapproved = false;
                    } else {
                        $scope.password.default     = false;
                        $scope.password.approved    = false;
                        $scope.password.disapproved = true;
                    }
                } 
            });

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.changePassInfo = {};
                $scope.oldPassword    = {
                    'default'  : true,
                };
                $scope.password       = {
                    'default'  : true,
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
