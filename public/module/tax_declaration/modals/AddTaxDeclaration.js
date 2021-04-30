define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('AddTaxDeclarationFactory', [
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

    app.service('AddTaxDeclarationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            // _this.getDetails = function (id) {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/getDetails?id=' + id);
            // }

            /**
             * `archive` Query string that will archive information.
             * @param  {[string]} id
             * @return {[route]}
             */
            // _this.archive = function (id) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/archiveAccess', {'id' : id});
            // }
        }
    ]);

    app.controller('AddTaxDeclarationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddTaxDeclarationFactory',
        'AddTaxDeclarationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddTaxDec');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                // blocker.start();

            	// Service.getDetails(ParamData.id).then( function (res) {
            	// 	$scope.accessList = angular.copy(res.data.access);

                //     $scope.jqDataTableOptions         = Factory.dtOptions();
                //     $scope.jqDataTableOptions.buttons = _btnFunc();
                //     $scope.jqDataTableOptions.data    = _formatAccess(res.data.access);
            	// });

                $scope.revision_nos = Factory.revNo
                $scope.brgys = Factory.brgys
                $scope.classifications = Factory.classifications
                $scope.units = Factory.units
                $scope.assessment_rates = Factory.assessment_rates
                $scope.td_nos = Factory.td_nos
            }

            $scope.addRow = function() {
                $scope.addTaxDec.details.push({
                    classification: null,
                    area: null,
                    unit: null,
                    market_value: null,
                    actual_use: null,
                    assessment_level: null,
                    assessed_value: null,
                })
            }

            $scope.removeRow = function(index) {
                $scope.addTaxDec.details.splice(index, 1)
                $scope.computeTotalMarketValue()
                $scope.computeTotalAssessedValue()
            }

            $scope.computeTotalMarketValue = function() {
                var total = 0

                angular.forEach($scope.addTaxDec.details, (v, k) => {
                    total += v.market_value
                })

                $scope.addTaxDec.total_market_value = angular.copy(total.toFixed(2))
            }

            $scope.computeTotalAssessedValue = function() {
                var total = 0

                angular.forEach($scope.addTaxDec.details, (v, k) => {
                    total += v.assessed_value
                })

                $scope.addTaxDec.total_assessed_value = angular.copy(total.toFixed(2))
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
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this tax declaration?",
                        function (res) {
                            if (res) {
                                // blocker.start();

                                // $timeout( function () {
                                //     Service.save($scope.addBrgy).then( function (res) {
                                //         if (res.data.status == true) {
                                //             Alertify.success("Classification successfully added!");

                                //             $uibModalInstance.close($scope.addBrgy);
                                //             blocker.stop();
                                //         } else {
                                //             Alertify.error("Classification already exist!");
                                //             blocker.stop();
                                //         }
                                //     });
                                // }, 1000);
                            }
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
                
                $scope.addTaxDec = {
                    details: [{
                        classification: null,
                        area: null,
                        unit: null,
                        market_value: null,
                        actual_use: null,
                        assessment_level: null,
                        assessed_value: null,
                    }],
                };

                $timeout(function() {
                    angular.element('#dated').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.addTaxDec.dated = angular.copy(formattedDate);
                        }
                    });
                }, 500);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
