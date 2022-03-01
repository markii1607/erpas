define([
    'app'
], function(app) {
    app.factory('mainFactory', [
        // 'alertify',
        function() {
            var Factory = {};

            // /**
            //  * `autoloadSettings` autoload params
            //  * @return {[type]}
            //  */
            // Factory.autoloadSettings = function() {
            //     // alertify
            //     alertify.logPosition('bottom right');
            //     alertify.theme('')
            // }

            /**
             * `templates` Modal templates.
             * @type {Array}
             */
            Factory.templates = [
                'module/main/modals/change_password.html',
            ];

            return Factory;
        }
    ]);

    app.service('mainService', [
        '$http',
        function($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            _this.getDetails = function() {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/getDetails');
            };

            /**
             * `signOut` Query string that will signout sessioned user.
             * @param  {[string]} input
             * @return {[object]}
             */
            _this.signOut = function() {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/signOut');
            }
             
            _this.changePassword = function(data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/changePassword', {'new_password' : data});
            }

            // /**
            //  * `signIn` Query string that will check credentials if valid or not.
            //  * @param  {[string]} input
            //  * @return {[object]}
            //  */
            // _this.signIn = function(input) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/Login/LoginService.php/checkLogin', input);
            // }
        }
    ]);

    app.controller('MainController', [
        '$scope',
        '$timeout',
        '$location',
        '$rootScope',
        '$uibModal',
        'alertify',
        'blockUI',
        '$filter',
        'mainService', 
        'mainFactory', 
        function($scope, $timeout, $location, $rootScope, $uibModal, Alertify, BlockUI, $filter, Service, Factory) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockerMain');

            /**
             * `_loadDetails` First things first.
             * @return {[mixed]}
             */
            _loadDetails = function() {
                blocker.start();
                Service.getDetails().then(function(res) {
                    blocker.stop();
                    if(res.data.check_session){
                        var mname = (res.data.user_details[0].mname != "") ? $filter('limitTo')(res.data.user_details[0].mname, 1, 0) + '. ' : "";

                        $scope.userDetails = angular.copy(res.data.user_details[0]);
                        $scope.userDetails.fullname = res.data.user_details[0].fname + ' ' + mname + res.data.user_details[0].lname;
                    }else{}
                });
            };

            $scope.redirectToModule = function(link, moduleAlias){
                if ($scope.userDetails.access_type == 1) {  // SUPER ADMIN
                    $location.path('main/' + link);

                } else if ($scope.userDetails.access_type == 2) {  // ADMIN
                    if (moduleAlias == 'UAC') { // User Access Config || Treasurer&Accounting's modules
                        Alertify.log('You don\'t have access to this module.');
                    } else {
                        $location.path('main/' + link);
                    }
                } else if ($scope.userDetails.access_type == 3) {  // TREASURER
                    if (moduleAlias == 'TREAS' || moduleAlias == 'RCR' || moduleAlias == 'CRR') {
                        $location.path('main/' + link);
                    } else {
                        Alertify.log('You don\'t have access to this module.');
                    }
                } else if ($scope.userDetails.access_type == 4) {  // ACCOUNTING
                    if (moduleAlias == 'ACCT') {
                        $location.path('main/' + link);
                    } else {
                        Alertify.log('You don\'t have access to this module.');
                    }
                }
            }

            /**
             * `signOut` signOut employee and destroy session session.
             * @return {[mixed]}
             */
             $scope.signOut = function() {
                Service.signOut().then(function(res) {
                    $timeout(function() {
                        $rootScope.check_session = false;
                        $location.path('/');
                    }, 100);
                });
            };

            /**
             * `changePassword` Changing of password.
             * @return {[mixed]}
             */
            $scope.changePassword = function() {
                Alertify.prompt("Enter New Password:" , function(res){
                    console.log(res);
                    blocker.start();
                    Service.changePassword(res).then(result => {
                        if (result.data.status) {
                            Alertify.success('You have successfully changed your password. Please re-login to your account.');
                            $scope.signOut();
                        } else {
                            Alertify.error('An error occurred while saving data. Please contact the administrator.');
                        }

                        blocker.stop();
                    });
                });
                // var paramData, modalInstance;

                // paramData = {};

                // modalInstance = $uibModal.open({
                //     animation: true,
                //     keyboard: false,
                //     backdrop: 'static',
                //     ariaLabelledBy: 'modal-title',
                //     ariaDescribedBy: 'modal-body',
                //     templateUrl: 'change_password.html',
                //     controller: 'ChangePasswordController',
                //     size: 'md',
                //     resolve: {
                //         paramData: function() {
                //             return paramData;
                //         }
                //     }
                // });

                // modalInstance.result.then(function(res) {
                //     Alertify.confirm("We recommend to re-login your account for security purposes.",
                //         function() {
                //             $scope.signOut();
                //         }
                //     );
                // }, function(res) {
                //     // Result when modal is dismissed
                // });
            }

            /**
             * `getCurrentDate` get current date.
             * @return {[mixed]}
             */
            //  $scope.getCurrentDate = function() {
            //     var today = new Date();
            //     var dd = today.getDate();
            //     var mm = today.getMonth() + 1; //January is 0!
            //     var yyyy = today.getFullYear();

            //     if (dd < 10) {
            //         dd = '0' + dd
            //     }

            //     if (mm < 10) {
            //         mm = '0' + mm
            //     }

            //     today = mm + '/' + dd + '/' + yyyy;
            //     return today;
            // }

            /**
             * `_init` First things first
             * @return {mixed}
             */
            _init = function() {
                // default settings
                // Factory.autoloadSettings();

                $scope.templates = Factory.templates;

                // default $scope settings
                $scope.login = {};

                $scope.header = {
                    title: '',
                    link: {
                        sub: '',
                        main: '',
                    },
                    showButton: false,
                }

                _loadDetails();
            };

            /**
             * Run _init() function
             */
            _init();
        }
    ]);
}); // end define