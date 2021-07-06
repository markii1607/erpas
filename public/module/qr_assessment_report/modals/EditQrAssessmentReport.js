define([
    'app'
], function (app) {
    app.factory('EditQrAssessmentReportFactory', [
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

            Factory.types = [
                {
                    name: 'Taxable',
                },
                {
                    name: 'Exempt',
                },
            ]

            Factory.subcategories = [
                {
                    no: 1,
                    type: 'Taxable',
                    name: 'Residential'
                },
                {
                    no: 2,
                    type: 'Taxable',
                    name: 'Agricultural'
                },
                {
                    no: 3,
                    type: 'Taxable',
                    name: 'Cultural'
                },
                {
                    no: 4,
                    type: 'Taxable',
                    name: 'Industrial'
                },
                {
                    no: 5,
                    type: 'Taxable',
                    name: 'Mineral'
                },
                {
                    no: 6,
                    type: 'Taxable',
                    name: 'Timber'
                },
                {
                    no: 7,
                    type: 'Taxable',
                    name: 'Special (Sec, 218(d))'
                },
                {
                    no: 7.1,
                    type: 'Taxable',
                    name: 'Machineries'
                },
                {
                    no: 7.2,
                    type: 'Taxable',
                    name: 'Cultural'
                },
                {
                    no: 7.3,
                    type: 'Taxable',
                    name: 'Scientific'
                },
                {
                    no: 7.4,
                    type: 'Taxable',
                    name: 'Hospital'
                },
                {
                    no: 7.5,
                    type: 'Taxable',
                    name: 'Local water Utilities Administraton (LWUA)'
                },
                {
                    no: 7.6,
                    type: 'Taxable',
                    name: 'GOCC - Water/Electric'
                },
                {
                    no: 7.7,
                    type: 'Taxable',
                    name: 'Recreation'
                },
                {
                    no: 7.8,
                    type: 'Taxable',
                    name: 'Others'
                },
                {
                    no: 1,
                    type: 'Exempt',
                    name: 'Government'
                },
                {
                    no: 2,
                    type: 'Exempt',
                    name: 'Religious'
                },
                {
                    no: 3,
                    type: 'Exempt',
                    name: 'Charitable'
                },
                {
                    no: 4,
                    type: 'Exempt',
                    name: 'Educational'
                },
                {
                    no: 5,
                    type: 'Exempt',
                    name: 'Machineries - Local Water District (LWD)'
                },
                {
                    no: 6,
                    type: 'Exempt',
                    name: 'Machineries - GOCC'
                },
                {
                    no: 7,
                    type: 'Exempt',
                    name: 'Pollution Control and Environmental Protection'
                },
                {
                    no: 8,
                    type: 'Exempt',
                    name: 'Reg. Coop. (R.A. 6938)'
                },
                {
                    no: 9,
                    type: 'Exempt',
                    name: 'Others'
                },
            ]

            return Factory;
        }
    ]);

    app.service('EditQrAssessmentReportService', [
        '$http',
        function ($http) {
            var _this = this;

            // _this.save = function (data) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueNoPropertyDec/MarketValueNoPropertyDecService.php/saveNewNoPropertyDec', data);
            // }
        }
    ]);

    app.controller('EditQrAssessmentReportController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditQrAssessmentReportFactory',
        'EditQrAssessmentReportService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditQrAssessmentReport');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                $scope.types = Factory.types
            }

            $scope.updateType = function() {
                delete $scope.editQrAssessmentReport.subcategory
                $scope.subcategories = angular.copy(Factory.subcategories.filter(obj => obj.type === $scope.editQrAssessmentReport.type.name))
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
                
                $scope.editQrAssessmentReport = {};

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
