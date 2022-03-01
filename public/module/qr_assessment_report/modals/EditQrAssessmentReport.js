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
                $scope.subcategories = angular.copy($scope.subcategoriesdata.filter(obj => obj.type === $scope.editQrAssessmentReport.type.name))
            }

            $scope.getTotal = function(data, value_type, classification = ''){
                
                if (data != undefined) {
                    var total = 0;
                    if (value_type == 'mv') {
                        if (classification == 'residential') {
                            total = parseFloat(data.land) + parseFloat(data.building.below_limit) + parseFloat(data.building.above_limit) + parseFloat(data.machinery) + parseFloat(data.others);
                        } else {
                            total = parseFloat(data.land) + parseFloat(data.building.building) + parseFloat(data.machinery) + parseFloat(data.others);
                        }
                    } else {
                        total = parseFloat(data.land) + parseFloat(data.building) + parseFloat(data.machinery) + parseFloat(data.others);
                    }

                    return total;
                } else {
                    return '';
                }

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
            $scope.saveUpdatedDetails = function () {
                Alertify.confirm("Are you sure you want to override data of the report?", function () {
                    var temp = {
                        type : $scope.editQrAssessmentReport.type.name,
                        data : $scope.editQrAssessmentReport.subcategory
                    }
                    $uibModalInstance.close(temp);
                });
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.editQrAssessmentReport = {};
                $scope.qrRecords = ParamData.data;

                $scope.subcategoriesdata = [
                    {
                        no: 1,
                        type: 'Taxable',
                        name: 'Residential',
                        attr: 'residential',
                        data: $scope.qrRecords.taxable.residential
                    },
                    {
                        no: 2,
                        type: 'Taxable',
                        name: 'Agricultural',
                        attr: 'agricultural',
                        data: $scope.qrRecords.taxable.agricultural
                    },
                    {
                        no: 3,
                        type: 'Taxable',
                        name: 'Cultural',
                        attr: 'cultural',
                        data: $scope.qrRecords.taxable.cultural
                    },
                    {
                        no: 4,
                        type: 'Taxable',
                        name: 'Industrial',
                        attr: 'industrial',
                        data: $scope.qrRecords.taxable.industrial
                    },
                    {
                        no: 5,
                        type: 'Taxable',
                        name: 'Mineral',
                        attr: 'mineral',
                        data: $scope.qrRecords.taxable.mineral
                    },
                    {
                        no: 6,
                        type: 'Taxable',
                        name: 'Timber',
                        attr: 'timber',
                        data: $scope.qrRecords.taxable.timber
                    },
                    {
                        no: 7,
                        type: 'Taxable',
                        name: 'Special (Sec, 218(d))',
                        attr: 'special',
                        data: $scope.qrRecords.taxable.special
                    },
                    {
                        no: 7.1,
                        type: 'Taxable',
                        name: 'Machineries',
                        attr: 'sp_machineries',
                        data: $scope.qrRecords.taxable.sp_machineries
                    },
                    {
                        no: 7.2,
                        type: 'Taxable',
                        name: 'Cultural',
                        attr: 'sp_cultural',
                        data: $scope.qrRecords.taxable.sp_cultural
                    },
                    {
                        no: 7.3,
                        type: 'Taxable',
                        name: 'Scientific',
                        attr: 'sp_scientific',
                        data: $scope.qrRecords.taxable.sp_scientific
                    },
                    {
                        no: 7.4,
                        type: 'Taxable',
                        name: 'Hospital',
                        attr: 'sp_hospital',
                        data: $scope.qrRecords.taxable.sp_hospital
                    },
                    {
                        no: 7.5,
                        type: 'Taxable',
                        name: 'Local water Utilities Administraton (LWUA)',
                        attr: 'sp_lwua',
                        data: $scope.qrRecords.taxable.sp_lwua
                    },
                    {
                        no: 7.6,
                        type: 'Taxable',
                        name: 'GOCC - Water/Electric',
                        attr: 'sp_gocc',
                        data: $scope.qrRecords.taxable.sp_gocc
                    },
                    {
                        no: 7.7,
                        type: 'Taxable',
                        name: 'Recreation',
                        attr: 'sp_recreation',
                        data: $scope.qrRecords.taxable.sp_recreation
                    },
                    {
                        no: 7.8,
                        type: 'Taxable',
                        name: 'Others',
                        attr: 'sp_others',
                        data: $scope.qrRecords.taxable.sp_others
                    },
                    {
                        no: 1,
                        type: 'Exempt',
                        name: 'Government',
                        attr: 'government',
                        data: $scope.qrRecords.exempt.government
                    },
                    {
                        no: 2,
                        type: 'Exempt',
                        name: 'Religious',
                        attr: 'religious',
                        data: $scope.qrRecords.exempt.religious
                    },
                    {
                        no: 3,
                        type: 'Exempt',
                        name: 'Charitable',
                        attr: 'charitable',
                        data: $scope.qrRecords.exempt.charitable
                    },
                    {
                        no: 4,
                        type: 'Exempt',
                        name: 'Educational',
                        attr: 'educational',
                        data: $scope.qrRecords.exempt.educational
                    },
                    {
                        no: 5,
                        type: 'Exempt',
                        name: 'Machineries - Local Water District (LWD)',
                        attr: 'machineries_lwd',
                        data: $scope.qrRecords.exempt.machineries_lwd
                    },
                    {
                        no: 6,
                        type: 'Exempt',
                        name: 'Machineries - GOCC',
                        attr: 'machineries_gocc',
                        data: $scope.qrRecords.exempt.machineries_gocc
                    },
                    {
                        no: 7,
                        type: 'Exempt',
                        name: 'Pollution Control and Environmental Protection',
                        attr: 'pcep',
                        data: $scope.qrRecords.exempt.pcep
                    },
                    {
                        no: 8,
                        type: 'Exempt',
                        name: 'Reg. Coop. (R.A. 6938)',
                        attr: 'reg_coop',
                        data: $scope.qrRecords.exempt.reg_coop
                    },
                    {
                        no: 9,
                        type: 'Exempt',
                        name: 'Others',
                        attr: 'others',
                        data: $scope.qrRecords.exempt.others
                    },
                ]

                console.log($scope.qrRecords);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
