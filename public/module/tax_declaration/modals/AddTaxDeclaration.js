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
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/getSelectionDetails');
            }

            _this.getMarketValues = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/getMvOfSelectedClassification', data);
            }

            _this.getNewTDNumber = function (rev_id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/getIncrementingTDNumber?rev_id=' + rev_id);
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/saveNewTaxDeclaration', data);
            }

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
                blocker.start();

            	Service.getDetails().then( function (res) {
                    if (res.data.revision_years != undefined) {
                        $scope.revision_nos     = res.data.revision_years;
                        $scope.brgys            = res.data.barangays;
                        $scope.classifications  = res.data.classifications;
                        $scope.td_nos           = res.data.td_nos;
                        $scope.approvers        = res.data.approvers;
                        $scope.units = [
                            { 'name' : 'sq.m' },
                            { 'name' : 'ha.' }
                        ]

                        // $scope.addTaxDec.td_no.td_code  = res.data.new_td_no;
                        $scope.addTaxDec.pin.prop_no    = res.data.new_pin;

                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
            	});
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

            $scope.getNewTDNumber = function(rev_id){
                blocker.start();
                Service.getNewTDNumber(rev_id).then(res => {
                    if (res.data.new_td_no != undefined) {
                        $scope.addTaxDec.td_no.td_code  = res.data.new_td_no;
                        blocker.stop();
                    } else {
                        Alertify.error("ERROR! Failed to auto-generate TD No.");
                        blocker.stop();
                    }
                });
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

            $scope.getMarketValues = function(index){
                if($scope.addTaxDec.details[index].sub_classification != undefined) delete $scope.addTaxDec.details[index].sub_classification;
                blocker.start();
                var tempData = {
                    'classification'    : $scope.addTaxDec.details[index].classification,
                    'revision_year'     : $scope.addTaxDec.td_no.rev,
                };
                Service.getMarketValues(tempData).then(res => {
                    if (res.data.market_values != undefined) {
                        $scope.market_values[index] = res.data.market_values;
                        if (res.data.market_values.length == 0) Alertify.alert('No data found for the Rev. Year ' + $scope.addTaxDec.td_no.rev.year + ' and ' + $scope.addTaxDec.details[index].classification.name + ' classification.');
                        blocker.stop();
                    } else {
                        Alertify.error('Failed to fetch MARKET VALUES data. Please contact the administrator.');
                        blocker.stop();
                    }
                });
            }

            $scope.computeMarketValue = function(index){
                console.log('type:', $scope.addTaxDec.type.name);
                console.log('inputUnit:', $scope.addTaxDec.details[index].unit.name);
                console.log('mvUnit:', $scope.addTaxDec.details[index].sub_classification.unit);

                delete $scope.addTaxDec.details[index].assessment_level;
                delete $scope.addTaxDec.details[index].assessed_value;
                if ($scope.addTaxDec.type.name != 'Machinery') {
                    if ($scope.addTaxDec.details[index].unit.name == $scope.addTaxDec.details[index].sub_classification.unit) {
                        console.log('same unit');
                        $scope.addTaxDec.details[index].market_value = $scope.addTaxDec.details[index].area * $scope.addTaxDec.details[index].sub_classification.market_value;
                    } else {
                        console.log('!same unit');
                        if (($scope.addTaxDec.details[index].unit.name == 'ha.') && ($scope.addTaxDec.details[index].sub_classification.unit == 'sq.m')) {
                            console.log('ha.');
                            var convertedValue = $scope.addTaxDec.details[index].area * 10000;     // convert area to ha.
                            $scope.addTaxDec.details[index].market_value = convertedValue * $scope.addTaxDec.details[index].sub_classification.market_value;
                        } else if (($scope.addTaxDec.details[index].unit.name == 'sq.m') && ($scope.addTaxDec.details[index].sub_classification.unit == 'ha.')) {
                            console.log('sq.m');
                            var convertedValue = $scope.addTaxDec.details[index].area * 0.0001;     // convert area to sq.m
                            $scope.addTaxDec.details[index].market_value = convertedValue * $scope.addTaxDec.details[index].sub_classification.market_value;
                        }
                    }

                    $scope.computeTotalMarketValue();
                }
            }

            $scope.computeAssessedValue = function(index){
                $scope.addTaxDec.details[index].assessed_value = $scope.addTaxDec.details[index].market_value * ($scope.addTaxDec.details[index].assessment_level/100);

                $scope.computeTotalAssessedValue();
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
                        function () {
                            blocker.start();

                            Service.save($scope.addTaxDec).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("Tax Declaration successfully added!");

                                    $uibModalInstance.close(res.data.rowData);
                                    blocker.stop();
                                } else {
                                    Alertify.error("An error occurred while saving! Please contact the administrator.");
                                    blocker.stop();
                                }
                            });
                        }
                    );
                } else {
                    Alertify.error("All fields marked with * are required!");
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
                    td_no : {
                        mun_code : '09'
                    },
                    pin   : {
                        prov_code : '031',
                        mun_code  : '09'
                    },
                    total_market_value   : 0,
                    total_assessed_value : 0,
                };

                $scope.market_values = [];

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
                    angular.element('#ordinance_date').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.addTaxDec.ordinance_date = angular.copy(formattedDate);
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
