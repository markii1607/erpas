define([
    'app'
], function (app) {
    app.factory('AddPropTaxDecFactory', [
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

    app.service('AddPropTaxDecService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.getLotOwners = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/ImprovementCertification/AddPropTaxDecService.php/getSelectionData', {'lot_no' : data});
            }

            _this.verifyRecords = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/ImprovementCertification/AddPropTaxDecService.php/getImprovementRecords', data);
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/ImprovementCertification/AddPropTaxDecService.php/saveCertificationData', data);
            }
        }
    ]);

    app.controller('AddPropTaxDecController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddPropTaxDecFactory',
        'AddPropTaxDecService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddPropTaxDec');

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

            $scope.getLotOwners = function(){
                blocker.start();
                Service.getLotOwners($scope.addPropTaxDec.lot_no).then(res => {
                    $scope.enabledOwnerSelection = true;
                    if (res.data.owners != undefined) {
                        $scope.lot_owners = res.data.owners;
                        if ($scope.lot_owners.length == 0) Alertify.alert("<b>No records found for Lot No#<i>" + $scope.addPropTaxDec.lot_no + "</i></b>");
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                })
            }

            $scope.reset = function(){
                $scope.withExistingRecords = false;
                delete $scope.addPropTaxDec.owner;
                delete $scope.lot_owners;
                delete $scope.improvement_records;
            }

            $scope.verifyRecords = function(){
                blocker.start();
                Service.verifyRecords($scope.addPropTaxDec).then(res => {
                    if (res.data.records != undefined) {
                        $scope.withExistingRecords = true;
                        $scope.improvement_records = res.data.records;
                        if ($scope.improvement_records.length == 0) Alertify.alert("<b>No records found!</b>");

                        $scope.users = res.data.users;
                        $scope.addPropTaxDec.prepared_by = $filter('filter')($scope.users, {
                            'id' : res.data.user_id
                        }, true)[0];
                        $scope.addPropTaxDec.verified_by = $filter('filter')($scope.users, {
                            'position' : 'Municipal Assessor'
                        }, true)[0];

                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                })
            }

            $scope.addRow = function() {
                $scope.improvement_records.push({
                    data_type   : 'new',
                    lot_no_new  : $scope.addPropTaxDec.lot_no
                })
            }

            $scope.removeRow = function(index) {
                $scope.improvement_records.splice(index, 1)
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function () {
                Alertify.confirm("Are you sure you want to add this certification?", function () {
                    blocker.start();
                    $scope.addPropTaxDec.improvements = $scope.improvement_records;
                    Service.save($scope.addPropTaxDec).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Classification successfully added!");

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
                
                $scope.addPropTaxDec = {};

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
