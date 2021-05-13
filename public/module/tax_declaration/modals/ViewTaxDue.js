define([
    'app'
], function (app) {
    app.factory('ViewTaxDueFactory', [
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

    app.service('ViewTaxDueService', [
        '$http',
        function ($http) {
            var _this = this;
           
        }
    ]);

    app.controller('ViewTaxDueController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'ViewTaxDueFactory',
        'ViewTaxDueService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockViewTaxDue');

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

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.data = ParamData.data;
                $scope.data.tax_due = parseFloat($scope.data.total_assessed_value) * 0.02;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
