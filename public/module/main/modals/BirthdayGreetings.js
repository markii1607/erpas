define([
    'app'
], function(app) {
    app.factory('birthdayGreetingsFactory', [
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

            return Factory;
        }
    ]);

    app.controller('BirthdayGreetingsController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'paramData',
        'birthdayGreetingsFactory',
        // function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, md5, ParamData, Factory, Service) {
        function($scope, $uibModalInstance, $timeout, ParamData, Factory, Service) {
            var _init, _loadDetails;

            _loadDetails = function() {
                $timeout(function() {
                    $uibModalInstance.dismiss();
                }, 3000);
            };

            _init = function() {
                // default settings
                Factory.autoloadSettings();

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }
    ]);
}); // end define