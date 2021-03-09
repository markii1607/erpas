define([
    'app'
], function(app) {
    app.factory('loginFactory', [
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
            Factory.templates = [];

            return Factory;
        }
    ]);

    app.service('loginService', [
        '$http',
        function($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            _this.getDetails = function() {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Login/LoginService.php/getDetails');
            };

            /**
             * `signIn` Query string that will check credentials if valid or not.
             * @param  {[string]} input
             * @return {[object]}
             */
            _this.signIn = function(input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Login/LoginService.php/checkLogin', input);
            }
        }
    ]);

    app.controller('LoginController', [
        '$scope',
        '$timeout',
        '$location',
        '$rootScope',
        '$uibModal',
        'blockUI',
        'alertify',
        // 'socket',
        'loginFactory',
        'loginService',
        // function ($scope, $timeout, $location, $rootScope, $uibModal, BlockUI, Alertify, Socket, Factory, Service) {
        function($scope, $timeout, $location, $rootScope, $uibModal, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockLogin');

            /**
             * `_loadDetails` First things first.
             * @return {[mixed]}
             */
            _loadDetails = function() {
                blocker.start();

                Service.getDetails().then(function(res) {
                    $rootScope.check_session = angular.copy(res.data.check_session);

                    if (res.data) {
                        if (res.data.session_logs.length !== 0) {
                            if (res.data.session_logs[0].status == "logged_off") {
                                $scope.hasExistingUserLogged = false;
                            } else {
                                $scope.hasExistingUserLogged = true;
                                $scope.login.username        = res.data.session_logs[0].unme.username
                            }
                        } else {
                            $scope.hasExistingUserLogged = false;
                        }
                    } else {
                        $scope.hasExistingUserLogged = false;
                    }

                    blocker.stop();
                });
            };

            /**
             * `signIn` Sign in function, checking of credential if valid or not.
             * @param  {Boolean} isValid
             * @return {[void]}
             */
            $scope.signIn = function(isValid) {
                blocker.start('Verifying...');

                if (isValid) {
                    Service.signIn($scope.login).then(function(res) {
                        $timeout(function() {
                            if (res.data.check_session) {
                                $rootScope.check_session = angular.copy(res.data.check_session);
                                Alertify.success('Successfully Signed in');

                                // Socket.emit('online-user', {
                                //     'full_name': res.data.full_name
                                // });

                                $location.path('/main/dashboard');
                            } else {
                                Alertify.error('Invalid Credentials.');
                            }

                            blocker.stop();
                        }, 100);
                    });
                } else {
                    $timeout(function() {
                        Alertify.error('Invalid Credentials.');
                        blocker.stop();
                    }, 100);
                }
            };

            /**
             * `_init` First things first
             * @return {mixed}
             */
            _init = function() {
                // default settings
                Factory.autoloadSettings();

                // default $scope settings
                $scope.login = {};

                $scope.templates = Factory.templates;

                _loadDetails();
            };

            /**
             * Run _init() function
             */
            _init();
        }
    ]);
}); // end define