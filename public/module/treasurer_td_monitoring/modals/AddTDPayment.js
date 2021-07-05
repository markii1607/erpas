define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('AddTDPaymentFactory', [
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

    app.service('AddTDPaymentService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/AddTDPaymentService.php/getSelectionDetails');
            }
            
            _this.searchRecords = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/AddTDPaymentService.php/getRecords', data);
            }
        }
    ]);

    app.controller('AddTDPaymentController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'AddTDPaymentFactory',
        'AddTDPaymentService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddTDPayment');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();
                Service.getDetails().then(res => {
                    if (res.data.td_nos != undefined) {
                        $scope.td_nos = res.data.td_nos;

                        blocker.stop();
                    } else {
                        Alertify.error("Failed to fetch data for selection. Please contact the administrator.");
                        blocker.stop();
                    }
                })
            }

            $scope.search = function(){
                if ($scope.addTdPayment.lot_no == undefined && $scope.addTdPayment.td_no == undefined && $scope.addTdPayment.declarant == undefined) {
                    Alertify.log("Please provide at least one(1) entry to proceed.");
                } else {
                    blocker.start();
                    Service.searchRecords($scope.addTdPayment).then(res => {
                        if (res.data.records != undefined) {
                            $scope.td_records = res.data.records;
                            if($scope.td_records.length == 0) Alertify.alert('<b>No records found!</b>');
                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                            blocker.stop();
                        }
                    })
                }
            }

            $scope.viewTaxDec = function (data) {
                var paramData, modalInstance;

                paramData = {
                    data,
                    server_base_url: APP.SERVER_BASE_URL,
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_tax_declaration.html',
                    controller      : 'ViewTaxDeclarationController',
                    size            : 'xlg',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.transactPayment = function () {
                var tempRecords = [];
                angular.forEach($scope.td_records, (value, key) => {
                    if (value.check != undefined) {
                        if (value.check) tempRecords.push(value);
                    }
                })

                if (tempRecords.length > 0) {
                    $scope.addTdPayment.records = tempRecords;
                    var paramData, modalInstance;
    
                    paramData = {
                        data : $scope.addTdPayment
                    }
    
                    modalInstance = $uibModal.open({
                        animation       : true,
                        keyboard        : false,
                        backdrop        : 'static',
                        ariaLabelledBy  : 'modal-title',
                        ariaDescribedBy : 'modal-body',
                        templateUrl     : 'payment_portal.html',
                        controller      : 'PaymentPortalController',
                        size            : 'xxxlg',
                        resolve         : {
                            paramData : function () {
                                return paramData;
                            }
                        }
                    });
    
                    modalInstance.result.then(function (res) {
                        $uibModalInstance.close(res);
                    }, function (res) {
                        // Result when modal is dismissed
                    });
                } else {
                    Alertify.log('Please select at least one(1) TD number.');
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
            $scope.save = function (isValid) {
               /*  if (isValid) {
                    blocker.start();
                    Service.checkTDNoDuplicate($scope.addTaxDec.td_no).then(tdChk => {
                        if (tdChk.data.hasDuplicate != undefined) {
                            if (!tdChk.data.hasDuplicate) {
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
                                Alertify.alert("Tax Declaration No. <u><b><i>" + tdChk.data.td_no + "</i></b></u> is already existing on the database. Please provide new TD No. to proceed.");
                            }
                            
                            blocker.stop();
                        } else {
                            Alertify.error('An error occurred while validating entries. Please contact the administrator.');
                            blocker.stop();
                        }

                    });
                } else {
                    Alertify.error("All fields marked with * are required!");
                } */
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.addTdPayment = {};

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
