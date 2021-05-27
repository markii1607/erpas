define([
    'app'
], function (app) {
    app.factory('AddNoPropertyDecFactory', [
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

    app.service('AddNoPropertyDecService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.verifyDeclareeRecords = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/NoPropertyCertification/AddNPCertificationService.php/verifyDeclareeRecords', {'declaree' : data});
            }

            _this.getSelectionDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/NoPropertyCertification/AddNPCertificationService.php/getSelectionDetails');
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/NoPropertyCertification/AddNPCertificationService.php/saveCertificationData', data);
            }
        }
    ]);

    app.controller('AddNoPropertyDecController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddNoPropertyDecFactory',
        'AddNoPropertyDecService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddNoPropertyDec');

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

            $scope.verifyDeclareeRecords = function(){
                blocker.start();
                Service.verifyDeclareeRecords($scope.addNoPropDec.declarees).then(res => {
                    if (res.data.records != undefined) {
                        blocker.stop();
                        if (res.data.records.length > 0) {
                            var strOwners = '';
                            angular.forEach(res.data.records, function (value, key) {
                                strOwners += '>> TD#<b>' + value.td_no + ' | </b> Owner: <b><i>' + value.owner + '</i></b>';
                            });

                            Alertify.alert('RECORDS FOUND!!! <br><br>' + strOwners);

                            $scope.withExistingRecords = true;
                        } else {
                            $scope.withExistingRecords = false;
                            blocker.start();
                            Service.getSelectionDetails().then(result => {
                                if (result.data.users != undefined) {
                                    $scope.users = result.data.users;
                                    $scope.addNoPropDec.prepared_by = $filter('filter')($scope.users, {
                                        'id' : result.data.user_id
                                    }, true)[0];
                                    $scope.addNoPropDec.verified_by = $filter('filter')($scope.users, {
                                        'position' : 'Municipal Assessor'
                                    }, true)[0];
                                    blocker.stop();
                                } else {
                                    Alertify.error("Failed to fetch users' data for selection. Please contact the administrator.");
                                    blocker.stop();
                                }
                            })
                        }
                        
                    } else {
                        Alertify.error("An error occurred while verifying records. Please contact the administrator.");
                        blocker.stop();
                    }
                });
            }

            $scope.reset = function(){
                $scope.withExistingRecords = true;
                delete $scope.addNoPropDec.requestor;
                delete $scope.addNoPropDec.or_no;
                delete $scope.addNoPropDec.amount_paid;
                delete $scope.addNoPropDec.purpose;
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function () {
                Alertify.confirm("Are you sure you want to save details for this certification?", function () {
                    blocker.start();

                    Service.save($scope.addNoPropDec).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Release of certification was successfully recorded!");

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
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.addNoPropDec = {};
                $scope.withExistingRecords = true;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
