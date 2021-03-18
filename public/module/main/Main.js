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
        'mainService', 
        function($scope, $timeout, $location, $rootScope, $uibModal, Service) {
            var _init, _loadDetails;

            /**
             * `_loadDetails` First things first.
             * @return {[mixed]}
             */
            _loadDetails = function() {
                Service.getDetails().then(function(res) {
                    if(res.data.check_session){
                        $scope.userDetails = res.data.user_details;
                    }else{}
                });
            };

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

                _loadDetails();
            };

            /**
             * Run _init() function
             */
            _init();
        }
    ]);
}); // end define