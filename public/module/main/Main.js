define([
    'app'
], function(app) {
    // app.factory('loginFactory', [
    //     // 'alertify',
    //     function() {
    //         var Factory = {};

    //         // /**
    //         //  * `autoloadSettings` autoload params
    //         //  * @return {[type]}
    //         //  */
    //         // Factory.autoloadSettings = function() {
    //         //     // alertify
    //         //     alertify.logPosition('bottom right');
    //         //     alertify.theme('')
    //         // }

    //         /**
    //          * `templates` Modal templates.
    //          * @type {Array}
    //          */
    //         Factory.templates = [
    //             'module/login/modals/registration.html',
    //         ];

    //         return Factory;
    //     }
    // ]);

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
        // 'loginFactory',
        '$filter',
        'mainService', 
        function($scope, $timeout, $location, $rootScope, $uibModal, $filter, Service) {
            var _init, _loadDetails;

            /**
             * `_loadDetails` First things first.
             * @return {[mixed]}
             */
            _loadDetails = function() {
                Service.getDetails().then(function(res) {
                    if(res.data.check_session){
                        var mname = (res.data.user_details[0].mname != "") ? $filter('limitTo')(res.data.user_details[0].mname, 1, 0) + '. ' : "";

                        $scope.userDetails = angular.copy(res.data.user_details[0]);
                        $scope.userDetails.fullname = res.data.user_details[0].fname + ' ' + mname + res.data.user_details[0].lname;
                    }else{}
                });
            };

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
            // $scope.changePassword = function() {
            //     var paramData, modalInstance;

            //     paramData = {};

            //     modalInstance = $uibModal.open({
            //         animation: true,
            //         keyboard: false,
            //         backdrop: 'static',
            //         ariaLabelledBy: 'modal-title',
            //         ariaDescribedBy: 'modal-body',
            //         templateUrl: 'change_password.html',
            //         controller: 'ChangePasswordController',
            //         size: 'md',
            //         resolve: {
            //             paramData: function() {
            //                 return paramData;
            //             }
            //         }
            //     });

            //     modalInstance.result.then(function(res) {
            //         Alertify.confirm("We recommend to re-login your account for security purposes.",
            //             function(res) {
            //                 if (res) {
            //                     $scope.signOut();
            //                 }
            //             }
            //         );
            //     }, function(res) {
            //         // Result when modal is dismissed
            //     });
            // }

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

                // $scope.templates = Factory.templates;

                // default $scope settings
                $scope.login = {};

                $scope.location = $location.path();
                $scope.header = {
                    title: 'Main',
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