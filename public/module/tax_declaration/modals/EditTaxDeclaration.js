define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('EditTaxDeclarationFactory', [
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

    app.service('EditTaxDeclarationService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.getDetails = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/EditTaxDeclarationService.php/getTDDetails?id=' + id);
            }

            _this.getMarketValues = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/getMvOfSelectedClassification', data);
            }

            _this.getNewTDNumber = function (rev_id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/getIncrementingTDNumber?rev_id=' + rev_id);
            }

            _this.archive = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/EditTaxDeclarationService.php/archiveTdClassification?id=' + id);
            }

            _this.checkTDNoDuplicate = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/AddTaxDeclarationService.php/checkTDNoDuplicate', {td_no : data});
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/EditTaxDeclarationService.php/updateTaxDeclaration', data);
            }
        }
    ]);

    app.controller('EditTaxDeclarationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditTaxDeclarationFactory',
        'EditTaxDeclarationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditTaxDec');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

            	Service.getDetails(ParamData.data.id).then( function (res) {
                    if (res.data.td_classifications != undefined) {
                        $scope.editTaxDec.details   = res.data.td_classifications;
                        $scope.revision_nos         = res.data.revision_years;
                        $scope.brgys                = res.data.barangays;
                        $scope.classifications      = res.data.classifications;
                        $scope.td_nos               = res.data.td_nos;
                        $scope.approvers            = res.data.approvers;
                        $scope.units = [
                            { 'name' : 'sq.m' },
                            { 'name' : 'ha.' }
                        ]

                        _formatTdData($scope.editTaxDec.details);

                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
            	});
            }

            _formatTdData = function(data){
                angular.forEach(data, (value, key) => {
                    $scope.editTaxDec.details[key].uv = {
                        area    : value.unit_value,
                        unit    : $filter('filter')($scope.units, {
                            'name' : value.uv_unit_measurement
                        }, true)[0]
                    };
                    $scope.editTaxDec.details[key].unit = $filter('filter')($scope.units, {
                        'name' : value.unit_measurement
                    }, true)[0];
                    // $scope.getMarketValues(key, 'init');
                });

                $scope.editTaxDec.td_no = {
                    'rev'       : $scope.editTaxDec.revision_year,
                    'mun_code'  : $scope.editTaxDec.td_number[1],
                    'brgy'      : $filter('filter')($scope.brgys, {
                        'code'  : $scope.editTaxDec.td_number[2]
                    }, true)[0],
                    'td_code'   : $scope.editTaxDec.td_number[3]
                }
                if($scope.editTaxDec.td_number[4] != undefined) $scope.editTaxDec.td_no.td_code2 = $scope.editTaxDec.td_number[4];

                $scope.editTaxDec.pin = {
                    'prov_code'  : $scope.editTaxDec.pi_number[0],
                    'mun_code'  : $scope.editTaxDec.pi_number[1],
                    'brgy_code'      : $filter('filter')($scope.brgys, {
                        'code'  : $scope.editTaxDec.pi_number[2]
                    }, true)[0],
                    'prop_no'   : $scope.editTaxDec.pi_number[4],
                }
                $scope.editTaxDec.pin.brgy_code.no_of_sections = $scope.editTaxDec.pi_number[3];
                if($scope.editTaxDec.pi_number[5] != undefined) $scope.editTaxDec.pin.bldg_no = $scope.editTaxDec.pi_number[5];
            }

            $scope.addRow = function() {
                $scope.editTaxDec.details.push({
                    classification  : null,
                    area            : null,
                    unit            : null,
                    market_value    : null,
                    actual_use      : null,
                    assessment_level: null,
                    assessed_value  : null,
                    data_type       : 'new'
                })
            }

            $scope.removeRow = function(index) {
                if ($scope.editTaxDec.details[index].data_type == 'saved') {
                    Alertify
                    .okBtn("Yes")
                    .cancelBtn("No")
                    .confirm("Are you sure you want to delete the selected row from the database?", function(){
                        blocker.start();
                        Service.archive($scope.editTaxDec.details[index].id).then(res => {
                            if (res.data.status) {
                                Alertify.success("Successfully deleted row!");
                                $scope.editTaxDec.details.splice(index, 1);
                                $scope.computeTotalMarketValue();
                                $scope.computeTotalAssessedValue();

                                blocker.stop();
                            } else {
                                Alertify.error("An error occurred while saving. Please contact the administrator.");
                                blocker.stop();
                            }
                        });
                    });
                } else if ($scope.editTaxDec.details[index].data_type == 'new') {
                    $scope.editTaxDec.details.splice(index, 1)
                    $scope.computeTotalMarketValue()
                    $scope.computeTotalAssessedValue()
                }
            }

            $scope.getNewTDNumber = function(rev_id){
                blocker.start();
                Service.getNewTDNumber(rev_id).then(res => {
                    if (res.data.new_td_no != undefined) {
                        $scope.editTaxDec.td_no.td_code  = res.data.new_td_no;
                        blocker.stop();
                    } else {
                        Alertify.error("ERROR! Failed to auto-generate TD No.");
                        blocker.stop();
                    }
                });
            }

            $scope.computeTotalMarketValue = function() {
                var total = 0

                angular.forEach($scope.editTaxDec.details, (v, k) => {
                    total += v.market_value
                })

                $scope.editTaxDec.total_market_value = angular.copy(total.toFixed(2))
            }

            $scope.computeTotalAssessedValue = function() {
                var total = 0

                angular.forEach($scope.editTaxDec.details, (v, k) => {
                    total += v.assessed_value
                })

                $scope.editTaxDec.total_assessed_value = angular.copy(total.toFixed(2))
            }

            /* $scope.getMarketValues = function(index, fnc = ''){
                if($scope.editTaxDec.details[index].sub_classification != undefined && fnc == '') delete $scope.editTaxDec.details[index].sub_classification;
                blocker.start();
                var tempData = {
                    'classification'    : $scope.editTaxDec.details[index].classification,
                    'revision_year'     : $scope.editTaxDec.revision_year,
                };
                Service.getMarketValues(tempData).then(res => {
                    if (res.data.market_values != undefined) {
                        $scope.market_values[index] = res.data.market_values;
                        if (res.data.market_values.length == 0) Alertify.alert('No data found for the Rev. Year ' + $scope.editTaxDec.td_no.rev.year + ' and ' + $scope.editTaxDec.details[index].classification.name + ' classification.');
                        blocker.stop();
                    } else {
                        Alertify.error('Failed to fetch MARKET VALUES data. Please contact the administrator.');
                        blocker.stop();
                    }
                });
            } */

            $scope.computeMarketValue = function(index){

                // delete $scope.editTaxDec.details[index].assessment_level;
                // delete $scope.editTaxDec.details[index].assessed_value;
                if ($scope.editTaxDec.details[index].unit.name == $scope.editTaxDec.details[index].uv.unit.name) {
                    console.log('same unit');
                    $scope.editTaxDec.details[index].market_value = $scope.editTaxDec.details[index].area * $scope.editTaxDec.details[index].uv.area;
                } else {
                    console.log('!same unit');
                    if (($scope.editTaxDec.details[index].unit.name == 'ha.') && ($scope.editTaxDec.details[index].uv.unit.name == 'sq.m')) {
                        console.log('ha.');
                        var convertedValue = $scope.editTaxDec.details[index].area * 10000;     // convert area to ha.
                        $scope.editTaxDec.details[index].market_value = convertedValue * $scope.editTaxDec.details[index].uv.area;
                    } else if (($scope.editTaxDec.details[index].unit.name == 'sq.m') && ($scope.editTaxDec.details[index].uv.unit.name == 'ha.')) {
                        console.log('sq.m');
                        var convertedValue = $scope.editTaxDec.details[index].area * 0.0001;     // convert area to sq.m
                        $scope.editTaxDec.details[index].market_value = convertedValue * $scope.editTaxDec.details[index].uv.area;
                    }
                }

                $scope.computeTotalMarketValue();
                $scope.computeAssessedValue(index);
            }

            $scope.computeAssessedValue = function(index){
                $scope.editTaxDec.details[index].assessed_value = $scope.editTaxDec.details[index].market_value * ($scope.editTaxDec.details[index].assessment_level/100);

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
             * `update` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.update = function (isValid) {
                if (isValid) {
                    console.log($scope.editTaxDec);
                    if ($scope.editTaxDec.td_no.td_code == $scope.editTaxDec.td_number[3]) {
                        _save();
                    } else {
                        blocker.start();
                        Service.checkTDNoDuplicate($scope.editTaxDec.td_no).then(tdChk => {
                            if (tdChk.data.hasDuplicate != undefined) {
                                if (!tdChk.data.hasDuplicate) {
                                    _save();
                                } else {
                                    Alertify.alert("Tax Declaration No. <u><b><i>" + tdChk.data.td_no + "</i></b></u> is already existing on the database. Please provide new TD No. to proceed.");
                                }
                                
                                blocker.stop();
                            } else {
                                Alertify.error('An error occurred while validating entries. Please contact the administrator.');
                                blocker.stop();
                            }
    
                        });
                    }
                    
                } else {
                    Alertify.error("All fields marked with * are required!");
                }
            };

            _save = function(){
                Alertify.confirm("Are you sure you want to add this tax declaration?",
                function () {
                    blocker.start();

                    Service.save($scope.editTaxDec).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Tax Declaration successfully edited!"); 

                            $uibModalInstance.close(res.data.rowData);
                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving! Please contact the administrator.");
                            blocker.stop();
                        }
                    });
                });
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.editTaxDec = ParamData.data;
                $scope.editTaxDec.tax_exempt = ($scope.editTaxDec.is_exempt == 1) ? 'exempt' : 'taxable';
                $scope.editTaxDec.prev_td    = $scope.editTaxDec.canceled_td;

                $scope.market_values = [];
                console.log($scope.editTaxDec);

                $timeout(function() {
                    angular.element('#dated').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.editTaxDec.dated = angular.copy(formattedDate);
                        }
                    });
                    angular.element('#ordinance_date').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.editTaxDec.ordinance_date = angular.copy(formattedDate);
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
