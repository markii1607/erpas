define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('PaymentPortalFactory', [
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

    app.service('PaymentPortalService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/AddTDPaymentService.php/savePaymentDetails', data);
            }

        }
    ]);

    app.controller('PaymentPortalController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'paramData',
        'alertify',
        'PaymentPortalFactory',
        'PaymentPortalService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, ParamData, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockPaymentPortal');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                
            }

            $scope.addRow = function() {
                $scope.addPaymentDetail.records.push({
                    declarant: null,
                    location: null,
                    lot_no: null,
                    td_no: null,
                    land: null,
                    improvement: null,
                    total: null,
                    tax_due: null,
                    installment_no: null,
                    installment_payment: null,
                    full_payment: null,
                    penalty: null,
                    grand_total: null,
                })
            }

            $scope.removeRow = function(index) {
                $scope.addPaymentDetail.records.splice(index, 1)
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
                
                Alertify.confirm("Saving the following data will marked the TD Number/s as PAID. Continue?",
                    function () {
                        blocker.start();
                        
                        _formatSubmittedData();
                        Service.save($scope.addPaymentDetail).then( function (res) {
                            if (res.data.status) {
                                Alertify.success("You have successfully saved the payment details of the seleted TD Number/s!");

                                $uibModalInstance.close(res.data.rowData);
                                blocker.stop();
                            } else {
                                Alertify.error("An error occurred while saving! Please contact the administrator.");
                                blocker.stop();
                            }
                        });
                    }
                );
            };

            _formatSubmittedData = function(data) {
                angular.forEach($scope.td_records, (value, key) => {
                    $scope.addPaymentDetail.records.push({id : value.id});
                })
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.td_records = ParamData.data.records
                $scope.addPaymentDetail = {
                    records : [
                        {
                            declarant: null,
                            location: null,
                            lot_no: null,
                            td_no: null,
                            land: null,
                            improvement: null,
                            total: null,
                            tax_due: null,
                            installment_no: null,
                            installment_payment: null,
                            full_payment: null,
                            penalty: null,
                            grand_total: null,
                        }
                    ]
                };

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
