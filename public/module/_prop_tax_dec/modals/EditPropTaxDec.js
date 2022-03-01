define([
    'app'
], function (app) {
    app.factory('EditPropTaxDecFactory', [
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

            Factory.units = [
                {
                    id: 1,
                    name: 'sq.m'
                },
                {
                    id: 2,
                    name: 'ha'
                },
            ]

            return Factory;
        }
    ]);

    app.service('EditPropTaxDecService', [
        '$http',
        function ($http) {
            var _this = this;

            // _this.save = function (data) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueNoPropertyDec/MarketValueNoPropertyDecService.php/saveNewNoPropertyDec', data);
            // }
        }
    ]);

    app.controller('EditPropTaxDecController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditPropTaxDecFactory',
        'EditPropTaxDecService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditPropTaxDec');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                $scope.units = angular.copy(Factory.units)
            }

            $scope.addRow = function() {
                $scope.editPropTaxDec.arps.push({
                    arp_no: null,
                    declarant: null,
                    lot_no: null,
                    area: null,
                    unit: null,
                    market_value: null,
                    assessed_value: null,
                })
            }

            $scope.removeRow = function(index) {
                $scope.editPropTaxDec.arps.splice(index, 1)
            }

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
            $scope.update = function (isFormValid) {
                if (isFormValid) {
                    Alertify.confirm("Are you sure you want to update this certification?", function () {
                        // blocker.start();
    
                        // Service.save($scope.addClfn).then( function (res) {
                        //     if (res.data.status) {
                        //         Alertify.success("Classification successfully added!");
    
                        //         $uibModalInstance.close(res.data.rowData);
                        //         blocker.stop();
                        //     } else {
                        //         Alertify.error("An error occurred while saving! Please contact the administrator.");
                        //         blocker.stop();
                        //     }
                        // });
                    });
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
                
                $scope.editPropTaxDec = ParamData.data;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
