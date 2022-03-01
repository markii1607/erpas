define([
    'app'
], function (app) {
    app.factory('AddPropertiesDecFactory', [
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

    app.service('AddPropertiesDecService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/PropertiesDec/AddPropertiesDecService.php/getSelectionData');
            }

            _this.verifyRecords = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/PropertiesDec/AddPropertiesDecService.php/verifyRecords', data);
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/PropertiesDec/AddPropertiesDecService.php/saveCertificationData', data);
            }
        }
    ]);

    app.controller('AddPropertiesDecController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddPropertiesDecFactory',
        'AddPropertiesDecService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddPropTaxDec');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();
                Service.getDetails().then(res => {
                    if (res.data.owners != undefined) {
                        $scope.lot_owners = res.data.owners;

                        $scope.users = res.data.users;
                        $scope.addPropTaxDec.prepared_by = $filter('filter')($scope.users, {
                            'id' : res.data.user_id
                        }, true)[0];
                        $scope.addPropTaxDec.verified_by = $filter('filter')($scope.users, {
                            'position' : 'Municipal Assessor'
                        }, true)[0];

                        blocker.stop();
                    } else {
                        Alertify.error('<b>An error occurred while fetching data. Please contact the administrator.</b>');
                        blocker.stop();
                    }
                })
            }

            $scope.changeEntryType = function(type){
                if (type == 'auto') {
                    $scope.addPropTaxDec.chk_auto   = true;
                    $scope.addPropTaxDec.chk_manual = false;

                } else if (type == 'manual') {
                    $scope.addPropTaxDec.chk_manual = true;
                    $scope.addPropTaxDec.chk_auto   = false;

                    $scope.property_records = [{}];
                }
            }

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            $scope.reset = function(){
                $scope.withExistingRecords = false;
            }

            $scope.verifyRecords = function(){
                blocker.start();
                Service.verifyRecords($scope.addPropTaxDec).then(res => {
                    if (res.data.records != undefined) {
                        $scope.withExistingRecords = true;
                        $scope.property_records = res.data.records;
                        if ($scope.property_records.length == 0) Alertify.alert("<b>No records found!</b>");

                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                })
            }

            $scope.addRow = function() {
                $scope.property_records.push({
                    data_type   : 'new',
                })
            }

            $scope.removeRow = function(index) {
                $scope.property_records.splice(index, 1)
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function () {
                Alertify.confirm("Are you sure you want to add this certification?", function () {
                    blocker.start();
                    $scope.addPropTaxDec.property_records = $scope.property_records;
                    Service.save($scope.addPropTaxDec).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Certification successfully added!");

                            $uibModalInstance.close(res.data.rowData);
                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving! Please contact the administrator.");
                            blocker.stop();
                        }
                    });
                });
            };

            /**
             *`addMoreItems` Adding more items for infinite scroll
            */
            //  $scope.addMoreItems = function () {
            //     $scope.infiniteScroll.currentItems += $scope.infiniteScroll.numToAdd;
            // };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                // $scope.infiniteScroll = {};
                // $scope.infiniteScroll.numToAdd = 5;
                // $scope.infiniteScroll.currentItems = 5;
                
                $scope.addPropTaxDec = {
                    chk_auto    : false,
                    chk_manual  : false,
                };

                $scope.withExistingRecords = false;
                $scope.enabledOwnerSelection = false;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
